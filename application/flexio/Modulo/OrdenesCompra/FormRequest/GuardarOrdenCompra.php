<?php

namespace Flexio\Modulo\OrdenesCompra\FormRequest;

use Illuminate\Http\Request;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Library\Util\FormRequest;
use Flexio\Library\Util\FlexioSession;
use Flexio\Library\Util\AuthUser;

//models
use Flexio\Modulo\OrdenesCompra\Models\OrdenesCompra;
use Flexio\Modulo\Politicas\Models\Politicas;
use Flexio\Modulo\Usuarios\Models\Usuarios;

//events compras
use Flexio\Modulo\OrdenesCompra\Events\OrdenesCompraEvents;
//listener
use Flexio\Modulo\OrdenesCompra\Listeners\UpdatePedido;

class GuardarOrdenCompra
{
    protected $request;
    protected $session;
    protected $disparador;
    protected $states_validations = ['1' => 'validatePorAprobar', '2' => 'validatePorFacturar', '3' => 'validateFacturadaParcial', '4' => 'validateFacturadoCompleto', '5' => 'validateAnulada'];

    public function __construct()
    {
        $this->request = Request::capture();
        $this->session = new FlexioSession();
        $this->disparador = new \Illuminate\Events\Dispatcher();  
        $this->AuthUser = new AuthUser();      
    }

    public function guardar($params = [])
    {
        $orden = !empty($params) ? $params : FormRequest::data_formulario($this->request->input());
        $orden['empresa_id'] = $this->session->empresaId();
        if(isset($orden['id']) && !empty($orden['id'])){
            return $this->update($orden);
        }
        //return $this->create($factura);// not support for this version
    }

    public function update($campos)
    {
        return Capsule::transaction(function () use ($campos) {
            $orden = OrdenesCompra::find($campos['id']);  
            if($this->AuthUser->is_owner() == false){          
            $this->update_validations($orden, $campos);
            }
            $campos['id_estado'] = $campos['estado_id'];
            $campos['id_empresa'] = $campos['empresa_id'];
            unset($campos['estado_id']);
            unset($campos['empresa_id']);
            $orden->update($campos);
            //dd($this->states_updaters($orden));
             if (!empty($orden->id_estado)){
                call_user_func_array([$this, 'eventoUpdatePedido'], [$orden]);
            }
            return $orden;
        });
    }

    /*private function states_updaters($orden)
    {        
        return $orden->estado->etiqueta == 'por_facturar' || $orden->estado->etiqueta == 'anulada';
    }*/

    private function update_validations($orden, $campos)
    {
        if($orden->id_estado != $campos['estado_id']){
            call_user_func_array([$this, $this->states_validations[$campos['estado_id']]], [$orden, $campos]);
        }
    }

    private function validatePorAprobar($orden, $campos)
    {
        if($orden->id_estado != '2')throw new \Exception("La orden Nro. {$orden->codigo} requiere estar 'Suspendida' antes de cambiar el estado");
        if(!count($this->getPoliticas($orden, $campos)))throw new \Exception("No tiene permisos (PA) para cambiar el estado de la a orden Nro. {$orden->codigo}");
    }

    private function validatePorFacturar($orden, $campos)
    {
        if($orden->id_estado != '1')throw new \Exception("La orden Nro. {$orden->codigo} requiere estar 'Por aprobar' antes de cambiar el estado");
        if(!count($this->getPoliticas($orden, $campos)))throw new \Exception("No tiene permisos (PA) para cambiar el estado de la a orden Nro. {$orden->codigo}");
    }

    private function validateFacturadaParcial($orden, $campos)
    {
        if($orden->id_estado != '1')throw new \Exception("La orden Nro. {$orden->codigo} requiere estar 'Por aprobar' antes de cambiar el estado");
        if(!count($this->getPoliticas($orden, $campos)))throw new \Exception("No tiene permisos (PA) para cambiar el estado de la a orden Nro. {$orden->codigo}");
    }

    private function validateFacturadoCompleto($orden, $campos)
    {
        if($orden->id_estado != '1')throw new \Exception("La orden Nro. {$orden->codigo} requiere estar 'Por aprobar' antes de cambiar el estado");
        if(!count($this->getPoliticas($orden, $campos)))throw new \Exception("No tiene permisos (PA) para cambiar el estado de la a orden Nro. {$orden->codigo}");
    }

    private function validateAnulada($orden, $campos)
    {
        if($orden->id_estado != '1')throw new \Exception("La orden Nro. {$orden->codigo} requiere estar 'Por aprobar' antes de cambiar el estado");
        if(!count($this->getPoliticas($orden, $campos)))throw new \Exception("No tiene permisos (PA) para cambiar el estado de la orden Nro. {$orden->codigo}");
    }

    private function getPoliticas($orden, $campos)
    {
        $usuario = Usuarios::find($this->session->usuarioId());
        $campos['role_id'] = count($usuario->roles_reales->first()) ? $usuario->roles_reales->first()->id : -1;
        $campos['categorias'] = count($orden->lines_items) ? $orden->lines_items->pluck('categoria_id') : [-1];        
        return Politicas::select('ptr_transacciones.*')->where(function($q) use ($orden, $campos){
            $q->where('ptr_transacciones.empresa_id', $campos['empresa_id']);
            $q->where('ptr_transacciones.role_id', $campos['role_id']);
            $q->where('ptr_transacciones.estado_id', 1);
            $q->whereHas('estado_politica', function($estado_politica) use ($orden, $campos){
                $estado_politica->where('ptr_transacciones_catalogo.estado1', $orden->id_estado);
                $estado_politica->where('ptr_transacciones_catalogo.estado2', $campos['estado_id']);
            });
        })
        ->join('ptr_transacciones_categoria', function($join){
            $join->on('ptr_transacciones_categoria.transaccion_id', "=", "ptr_transacciones.id");
        })
        ->where(function($aux) use ($orden, $campos){
            foreach($orden->lines_items as $orden_item){              
                $aux->where(function($aux) use ($orden, $campos){
                    $aux->whereIn('ptr_transacciones_categoria.categoria_id', $campos['categorias'] );
                    $aux->where('ptr_transacciones.monto_limite', ">=", $orden->monto);
                });
            }
        })
        ->groupBy('ptr_transacciones.id')
        ->havingRaw('count(distinct ptr_transacciones_categoria.categoria_id) = '.count(array_unique($campos['categorias']->toArray())))
        ->get();
    }

    public function eventoUpdatePedido($orden)
    {
        //listener handle
        $this->disparador->listen([
            OrdenesCompraEvents::class,
        ],
        UpdatePedido::class);
        $this->disparador->fire(new OrdenesCompraEvents($orden));
    }
}
