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

class GuardarCreditoAplicado
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
        $factura = FormRequest::data_formulario($this->request->input('campo'));
        $factura['empresa_id'] = $this->session->empresaId();
        return $this->crear($factura);
    }

    public function crear($campos)
    {
        return Capsule::transaction(function () use ($campos) {

            $factura = \Flexio\Modulo\FacturasCompras\Models\FacturaCompra::where(function($q) use ($campos){
                $q->deFiltro($campos);
            })->first();

            if(!isset($campos['total']))throw new \Exception('Debe ingresar el monto del c&eacute;dito a aplicar');
            if(!($campos['total'] > '0.00'))throw new \Exception('El monto del cr&eacute;dito a aplicar debe ser mayor que $0.00');
            if(round(str_replace(",", "", $campos['total']),2) > round($factura->saldo, 2))throw new \Exception('El monto del cr&eacute;dito a aplicar no puede ser mayor al saldo pendiente');

            $credito_aplicado_obj = new \Flexio\Modulo\CreditosAplicados\Models\CreditoAplicado($campos);
            $credito_aplicado = $factura->creditos_aplicados()->save($credito_aplicado_obj);
            //update status
            call_user_func_array(['Flexio\Modulo\FacturasCompras\FormRequest\GuardarCreditoAplicado', 'eventoAplicarCredito'], [$factura->fresh()]);
            //do transacctions
            $transaccionCreditoAplicado = new RealizarTransaccionCreditoAplicado($credito_aplicado);
            $transaccionCreditoAplicado->hacer();
            return $credito_aplicado;
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
