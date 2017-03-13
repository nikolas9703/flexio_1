<?php

namespace Flexio\Modulo\FacturasCompras\FormRequest;

use Illuminate\Http\Request;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Library\Util\FormRequest;
use Flexio\Library\Util\FlexioSession;

//models
use Flexio\Modulo\FacturasCompras\Models\FacturaCompra;
use Flexio\Modulo\Politicas\Models\Politicas;
use Flexio\Modulo\Usuarios\Models\Usuarios;

//transacctions
//transacciones
use Flexio\Modulo\FacturasCompras\Transacciones\FacturasComprasTransacciones;

//events compras
use Flexio\Modulo\FacturasCompras\Events\FacturaCompraEvents;
//listener
use Flexio\Modulo\FacturasCompras\Listeners\UpdateInvoice;

class GuardarFacturaCompra
{
    protected $request;
    protected $session;
    protected $disparador;
    protected $states_validations = ['13' => 'validatePorAprobar', '14' => 'validatePorPagar', '20' => 'validateSuspendida', '17' => 'validateAnulada'];
    protected $FacturasComprasTransacciones;

    public function __construct()
    {
        $this->request = Request::capture();
        $this->session = new FlexioSession();
        $this->disparador = new \Illuminate\Events\Dispatcher();
        $this->FacturasComprasTransacciones = new FacturasComprasTransacciones;
    }

    public function guardar($params = [])
    {
        $factura = !empty($params) ? $params : FormRequest::data_formulario($this->request->input());
        $factura['empresa_id'] = $this->session->empresaId();
        if(isset($factura['id']) && !empty($factura['id'])){
            return $this->update($factura);
        }
        //return $this->create($factura);// not support for this version
    }

    public function update($campos)
    {
        return Capsule::transaction(function () use ($campos) {
            $factura = FacturaCompra::find($campos['id']);
            $this->update_validations($factura, $campos);
            $factura->update($campos);

            //$this->FacturasComprasTransacciones->haceTransaccion($factura);
            if (!empty($factura->operacion_type) && count($factura->operacion) > 0 && $this->states_updaters($factura)){
                call_user_func_array([$this, 'eventoUpdateInvoice'], [$factura]);
            }
            return $factura;
        });
    }

    private function states_updaters($factura)
    {
        return $factura->estado->etiqueta == 'por_pagar' || $factura->estado->etiqueta == 'anulada';
    }

    private function update_validations($factura, $campos)
    {
        if($factura->estado_id != $campos['estado_id']){
            call_user_func_array([$this, $this->states_validations[$campos['estado_id']]], [$factura, $campos]);
        }
    }

    private function validatePorAprobar($factura, $campos)
    {
        if($factura->estado_id != '20')throw new \Exception("La factura Nro. {$factura->codigo} requiere estar 'Suspendida' antes de cambiar el estado");
        if(!count($this->getPoliticas($factura, $campos)))throw new \Exception("No tiene permisos (PA) para cambiar el estado de la a factura Nro. {$factura->codigo}");
    }

    private function validatePorPagar($factura, $campos)
    {
        if($factura->estado_id != '13')throw new \Exception("La factura Nro. {$factura->codigo} requiere estar 'Por aprobar' antes de cambiar el estado");
        if(!count($this->getPoliticas($factura, $campos)))throw new \Exception("No tiene permisos (PA) para cambiar el estado de la a factura Nro. {$factura->codigo}");
    }

    private function validateSuspendida($factura, $campos)
    {
        if($factura->estado_id != '13')throw new \Exception("La factura Nro. {$factura->codigo} requiere estar 'Por aprobar' antes de cambiar el estado");
        if(!count($this->getPoliticas($factura, $campos)))throw new \Exception("No tiene permisos (PA) para cambiar el estado de la a factura Nro. {$factura->codigo}");
    }

    private function validateAnulada($factura, $campos)
    {
        if($factura->estado_id != '13')throw new \Exception("La factura Nro. {$factura->codigo} requiere estar 'Por aprobar' antes de cambiar el estado");
        if(!count($this->getPoliticas($factura, $campos)))throw new \Exception("No tiene permisos (PA) para cambiar el estado de la a factura Nro. {$factura->codigo}");
    }

    private function getPoliticas($factura, $campos)
    {
        $usuario = Usuarios::find($this->session->usuarioId());
        $campos['role_id'] = count($usuario->roles_reales->first()) ? $usuario->roles_reales->first()->id : -1;
        $campos['categorias'] = count($factura->facturas_items) ? $factura->facturas_items->pluck('categoria_id') : [-1];
        return Politicas::select('ptr_transacciones.*')->where(function($q) use ($factura, $campos){
            $q->where('ptr_transacciones.empresa_id', $campos['empresa_id']);
            $q->where('ptr_transacciones.role_id', $campos['role_id']);
            $q->whereHas('estado_politica', function($estado_politica) use ($factura, $campos){
                $estado_politica->where('ptr_transacciones_catalogo.estado1', $factura->estado_id);
                $estado_politica->where('ptr_transacciones_catalogo.estado2', $campos['estado_id']);
            });
        })
        ->join('ptr_transacciones_categoria', function($join){
            $join->on('ptr_transacciones_categoria.transaccion_id', "=", "ptr_transacciones.id");
        })
        ->where(function($aux) use ($factura, $campos){
            foreach($factura->facturas_items as $factura_item){
                $aux->where(function($aux) use ($factura_item, $campos){
                    $aux->whereIn('ptr_transacciones_categoria.categoria_id', $campos['categorias'] );
                    $aux->where('ptr_transacciones.monto_limite', ">=", $factura_item->subtotal);
                });
            }
        })
        ->groupBy('ptr_transacciones.id')
        ->havingRaw('count(distinct ptr_transacciones_categoria.categoria_id) = '.count(array_unique($campos['categorias']->toArray())))
        ->get();
    }

    public function eventoUpdateInvoice($factura)
    {
        //listener handle
        $this->disparador->listen([
            FacturaCompraEvents::class,
        ],
        UpdateInvoice::class);
        $this->disparador->fire(new FacturaCompraEvents($factura));
    }
}
