<?php

namespace Flexio\Modulo\Pagos\Transacciones;

use Flexio\Repository\SysTransaccion\SysTransaccionRepository as SysTransaccionRepository;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable as AsientoContable;
use Flexio\Modulo\Cajas\Models\Cajas;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Anticipos\Events\RealizarTransaccionAnticipo;
use Flexio\Modulo\Anticipos\Events\ActualizarCreditoProveedor;
use Flexio\Modulo\Anticipos\Transacciones\AnticipoAnularTransaccion;
use Flexio\Modulo\Proveedores\Service\AnularCredito;


class PagosProveedor {

    protected $SysTransaccionRepository;

    public function __construct() {
        $this->SysTransaccionRepository = new SysTransaccionRepository();
    }

    public function haceTransaccion($pago)
    {
        $clause      = [
            "empresa_id"    => $pago->empresa_id,
            "nombre"        => 'TransaccionPago'.'-'.$pago->codigo.'-'.$pago->empresa_id,
        ];
        $transaccion = $this->SysTransaccionRepository->findBy($clause);

        if(!count($transaccion))
        {
            $sysTransaccion         = new SysTransaccionRepository;
            $modeloSysTransaccion   = "";
            $infoSysTransaccion     = array('codigo'=>'Sys','nombre'=>$clause["nombre"],'empresa_id'=>$pago->empresa_id,'linkable_id'=>$pago->id,'linkable_type'=> get_class($pago));

            Capsule::transaction(function() use($sysTransaccion, $modeloSysTransaccion, $infoSysTransaccion, $pago){
                $modeloSysTransaccion =  $sysTransaccion->create($infoSysTransaccion);
                $modeloSysTransaccion->transaccion()->saveMany($this->transacciones($pago));
                if(is_null($modeloSysTransaccion)){throw new \Exception('No se pudo hacer la transacción');}
            });

        }

    }

    public function deshaceTransaccion($pago)
    {
        $clause      = [
            "empresa_id"    => $pago->empresa_id,
            "nombre"        => 'TransaccionPago'.'-'.$pago->codigo.'-'.$pago->empresa_id,
        ];
        $transaccion = $this->SysTransaccionRepository->findBy($clause);

        if(count($transaccion))
        {
            Capsule::transaction(function() use($transaccion){
                $transaccion->transaccion()->delete();
                $transaccion->delete();
                if(is_null($transaccion)){throw new \Exception('No se pudo hacer la transacción');}
            });
        }

        //logic changed -> card 8 from flexio 2017 board
        /*if($pago->empezable_type == "anticipo"){
            foreach($pago->anticipo as $anticipo){
                $actualizarCreditoProveedor = new AnularCredito($anticipo);
                $actualizarCreditoProveedor->hacer();
            }
        }*/
    }

    public function transacciones($pago)
    {
        return array_merge($this->_debito($pago),$this->_credito($pago));
    }


    private function _debito($pago)
    {

        $cuenta_id  = $this->_getCuentaIdDebito($pago);
        $asientos   = [];

            if($pago->empezable_type == "anticipo"){
                foreach($pago->anticipo as $anticipo)
                {
                    $asientos[] = new AsientoContable([
                        'codigo'        => $pago->codigo,
                        'nombre'        => $pago->codigo. ' - '.$anticipo->codigo .' - '.$pago->nombre_proveedor,
                        'debito'        => $anticipo->pivot->monto_pagado,
                        'centro_id'     => $anticipo->centro_contable_id,
                        'cuenta_id'     => $cuenta_id,
                        'empresa_id'    => $pago->empresa_id,
                        'created_at'   => $pago->fecha_pago_data_base
                    ]);

                    //logic changed -> card 8 from flexio 2017 board
                    /*
                    $actualizarCreditoProveedor = new ActualizarCreditoProveedor($anticipo);
                    $actualizarCreditoProveedor->hacer();
                    */
               }
            }else if($pago->empezable_type == "movimiento_monetario"){
              //DEBITO
              foreach($pago->retiros as $retiro) {
                //Recorrer items
                $asientos = $this->retiroItems($retiro, $pago);
              }
            }else{
                foreach($pago->facturas as $factura)
                {
                    $asientos[] = new AsientoContable([
                        'codigo'        => $pago->codigo,
                        'nombre'        => $pago->codigo. ' - '.$factura->codigo.' - '.$pago->nombre_proveedor,
                        'debito'        => $factura->pivot->monto_pagado,
                        'centro_id'     => $factura->centro_contable_id,
                        'cuenta_id'     => $cuenta_id,
                        'empresa_id'    => $pago->empresa_id,
                        'created_at'    => $pago->fecha_pago_data_base
                    ]);
                }
            }

        return $asientos;
    }

    private function _credito($pago){
        $cuenta_id  = $this->_getCuentaIdCredito($pago);
        $asientos   = [];
        if($pago->empezable_type == "anticipo"){
            foreach($pago->anticipo as $anticipo)
            {
                //hacer transaccion del anticipo.
                $transaccionAnticipo = new RealizarTransaccionAnticipo($anticipo);
                $transaccionAnticipo->hacer();
                //actualizar credito proveedor.
                //logic changed -> card 8 from flexio 2017 board
                /*
                $actualizarCreditoProveedor = new ActualizarCreditoProveedor($anticipo);
                $actualizarCreditoProveedor->hacer();
                */
                $asientos[] = new AsientoContable([
                    'codigo'        => $pago->codigo,
                    'nombre'        => $pago->codigo. ' - '.$anticipo->codigo.' - '.$pago->nombre_proveedor,
                    'credito'       => $pago->monto_pagado,
                    'cuenta_id'     => $cuenta_id,
                    'centro_id'     => $anticipo->centro_contable_id,
                    'empresa_id'    => $pago->empresa_id,
                    'created_at'    => $pago->fecha_pago_data_base
                ]);
            }
        }else if($pago->empezable_type == "movimiento_monetario"){
          foreach($pago->retiros as $retiro)
          {
              $asientos[] = new AsientoContable([
                  'codigo'        => $pago->codigo,
                  'nombre'        => $pago->codigo. ' - '.$retiro->codigo,
                  'credito'       => $pago->monto_pagado,
                  'cuenta_id'     => $cuenta_id,
                  'created_at'    => $pago->fecha_pago_data_base, 
                  'empresa_id'    => $pago->empresa_id
              ]);
          }
        }else{
            foreach($pago->facturas as $factura)
            {
                $asientos[] = new AsientoContable([
                    'codigo'        => $pago->codigo,
                    'nombre'        => $pago->codigo. ' - '.$factura->codigo.' - '.$pago->nombre_proveedor,
                    'credito'       => $pago->monto_pagado,
                    'centro_id'     => $factura->centro_contable_id,
                    'cuenta_id'     => $cuenta_id,
                    'empresa_id'    => $pago->empresa_id,
                    'created_at'    => $pago->fecha_pago_data_base
                ]);
            }
        }
        if($pago->depositable_type == 'Flexio\Modulo\Cajas\Models\Cajas'){

            $caja = Cajas::find($pago->depositable_id);
            $cuenta_id = $caja->cuenta_id;
            $caja->saldo =  $caja->saldo - $pago->monto_pagado;
            $caja->save();
        }
        return $asientos;
    }

    private function retiroItems($retiro, $pago){
      $asientos=array();
      foreach($retiro->items as $item)
      {
          $asientos[] = new AsientoContable([
            'codigo'        => $pago->codigo,
            'nombre'        => $pago->codigo. ' - '.$retiro->codigo.' - '.$pago->nombre_proveedor,
            'debito'        => $item->debito,
            'centro_id'     => $item->centro_id,
            'cuenta_id'     => $item->cuenta_id,
            'empresa_id'    => $pago->empresa_id,
            'created_at'    => $pago->fecha_pago_data_base
          ]);
      }
      return $asientos;
    }


    private function _getCuentaIdDebito($pago)
    {
		if($pago->formulario != 'honorario' || $pago->formulario != 'remesa'){
			return $pago->empresa->cuenta_por_pagar_proveedores->first()->cuenta_id;
        }

    }

    private function _getCuentaIdCredito($pago)
    {
        $cuenta_id = 0;

        if($pago->metodo_pago[0]->tipo_pago == 'aplicar_credito' || $pago->empezable_type == "anticipo")
        {
            $cuenta_id = $pago->empresa->cuenta_abonar_proveedores->first()->cuenta_id;
        }
        elseif($pago->depositable_type == "caja"){
            $cuenta_id = $pago->empresa->cuenta_caja_menuda->cuenta_id;
        }
        else
        {
            $cuenta_id = $pago->depositable_id;
        }
        return $cuenta_id;
    }

}
