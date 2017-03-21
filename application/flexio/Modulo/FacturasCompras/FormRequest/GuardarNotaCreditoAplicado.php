<?php

namespace Flexio\Modulo\FacturasCompras\FormRequest;

use Illuminate\Http\Request;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Library\Util\FormRequest;
use Flexio\Library\Util\FlexioSession;

//events compras
use Flexio\Modulo\FacturasCompras\Events\FacturaCompraEvents;
use Flexio\Modulo\FacturasCompras\Events\RealizarTransaccionCreditoAplicado;
//listener
use Flexio\Modulo\FacturasCompras\Listeners\CreditoAplicado;

class GuardarNotaCreditoAplicado
{
    protected $request;
    protected $session;
    protected $tipo_deposito;
    protected $disparador;
    protected $tipo_anticipable;
    protected $empezable;

    public function __construct()
    {
        $this->request = Request::capture();
        $this->session = new FlexioSession();
        $this->disparador = new \Illuminate\Events\Dispatcher();
    }

    public function guardar()
    {
        $nota_credito_aplicada = FormRequest::data_formulario($this->request->input('campo'));
        $nota_credito_aplicada['empresa_id'] = $this->session->empresaId();
        return $this->crear($nota_credito_aplicada);
    }

    public function crear($campos)
    {
        return Capsule::transaction(function () use ($campos) {

            $factura = \Flexio\Modulo\FacturasCompras\Models\FacturaCompra::where(function($q) use ($campos){
                $q->deFiltro($campos);
            })->first();

            //to Validators class
            $suma_totales = array_reduce($campos['pagos'], function($result, $pago){
                return $result + round(str_replace(",", "", $pago['total']));
            });
            $credito_favor = count($factura->proveedor) ? $factura->proveedor->credito : 0;

            if(!($suma_totales > 0))throw new \Exception('El total de los montos a aplicar debe ser mayor que $0.00');
            if($suma_totales > $credito_favor)throw new \Exception('El total de los montos a aplicar no puede ser mayor al cr&eacute;dito del proveedor');
            if(round($suma_totales, 2) > round($factura->saldo, 2))throw new \Exception('El monto del cr&eacute;dito a aplicar no puede ser mayor al saldo pendiente');

            foreach($campos['pagos'] as $pago){
                if(empty($pago['total']))continue;
                $pago = array_merge($pago, ['empresa_id' => $campos['empresa_id']]);
                $credito_aplicado_obj = new \Flexio\Modulo\CreditosAplicados\Models\CreditoAplicado($pago);
                $credito_aplicado = $factura->creditos_aplicados()->save($credito_aplicado_obj);
                //update status
                call_user_func_array([$this, 'eventoAplicarCredito'], [$factura->fresh()]);

                //update vendor credit
                if(count($factura->proveedor)){
                    $factura->proveedor->credito -= $credito_aplicado->total;
                    $factura->proveedor->save();
                }

                //do transacctions
                $transaccionCreditoAplicado = new RealizarTransaccionCreditoAplicado($credito_aplicado);
                $transaccionCreditoAplicado->hacer();
                //return $credito_aplicado;
            }
            return true;

        });
    }


    public function eventoAplicarCredito($factura)
    {
        //listener handle
        $this->disparador->listen([
            FacturaCompraEvents::class,
        ],
        CreditoAplicado::class);
        $this->disparador->fire(new FacturaCompraEvents($factura));
    }
}
