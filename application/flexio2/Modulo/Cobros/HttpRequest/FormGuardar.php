<?php

namespace Flexio\Modulo\Cobros\HttpRequest;

use Illuminate\Http\Request;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

use Flexio\Library\Util\FormRequest;
use Flexio\Library\Util\FlexioSession;
use Flexio\Modulo\Cobros\Models\Cobro;
use Flexio\Modulo\Cobros\Events\ActualizarCreditoCliente;
use Flexio\Modulo\Cobros\Events\ActualizarEstadoFactura;
use Flexio\Modulo\Cobros\Events\ActualizarSaldoCaja;
use Flexio\Strategy\Transacciones\Transaccion;
use Flexio\Modulo\Cobros\Transaccion\TransaccionCobro;


class FormGuardar{

    protected $request;
    protected $session;
    protected $tipo_deposito;
    protected $disparador;

    function __construct(){
        $this->request = Request::capture();
        $this->session = new FlexioSession;
        $this->tipo_deposito = ['banco'=>'Flexio\Modulo\Contabilidad\Models\Cuentas', 'caja'=>'Flexio\Modulo\Cajas\Models\Cajas'];
        $this->disparador = new \Illuminate\Events\Dispatcher();
    }

    function guardar(){
          $cobro = FormRequest::data_formulario($this->request->input('campo'));
          $facturas = collect(FormRequest::array_filter_dos_dimenciones($this->request->input('factura')));
          $metodo_cobros = $this->request->input('metodo_pago');

          //dd($this->request->all());
          //if(isset($cobro['tipo_deposito']))$cobro['depositable_type'] = $this->tipo_deposito[$cobro['tipo_deposito']];
          
          if(isset($cobro['id'])){
              return $this->actualizar($cobro, $facturas, $metodo_cobros);
          }
          $empezable = $this->request->input('empezable');

          $cobro['empezable_id'] = $empezable['empezable_id'];
          $cobro['empezable_type'] = $empezable['empezable_type'];
          $cobro['codigo'] = $this->getLastCodigo();
          $cobro['estado'] = 'aplicado';
          //estado
          $factura_ids = array_get($facturas,'cobrable_id');
          return $this->crear($cobro, $facturas, $metodo_cobros);
    }


    function crear($campos, $facturas, $metodo_cobros){
            return  Capsule::transaction(function() use($campos, $facturas, $metodo_cobros){

                //1. se crea el cobros
                $cobro = Cobro::create($campos);

                //se obtiene los ids de las facturas para cambiarle el estado
                $factura_ids = $facturas->map(function($item){
                    return $item['cobrable_id'];
                });
                //registro de metodo de pago
                $metodoCobros =  FormatoMetodoCobro::formato($metodo_cobros);
                $cobro->metodo_cobro()->saveMany($metodoCobros);

                //se registran los cobros a las facturas
                $cobrables = FormatoCobrables::formato($facturas);
                $cobro->cobros_facturas()->saveMany($cobrables);
                //actualizar credito del cliente; cunando se paga con credito
                ActualizarCreditoCliente::actualizar($cobro);
                //cambiar es estado de la factura
                ActualizarEstadoFactura::manupilarEstado($factura_ids);
                //actualizar saldo de caja
                if($campos['depositable_type']=='caja'){
                 ActualizarSaldoCaja::nuevoSaldo($cobro->depositable_id, $cobro->monto_pagado);
                }
                //realizar transaccion
                $transaccion = new Transaccion;
                $transaccion->hacerTransaccion($cobro->fresh(), new TransaccionCobro);

            return $cobro;
        });
    }

    function actualizar($campos, $items, $metodo_cobros)
    {
        return  Capsule::transaction(function() use($campos, $items, $metodo_cobros){
            $cobro = Cobro::find($campos['id']);
            $cobro->update(['estado'=>$campos['estado']]);
            return $cobro;
        });
    }

    function getLastCodigo(){
        $clause = ['empresa_id' => $this->session->empresaId()];
        $year = Carbon::now()->format('y');
        $cobro = Cobro::where($clause)->get()->last();
        $codigo = (int)str_replace('PAY'.$year, "", $cobro->codigo);
        return $codigo + 1;
      }
}
