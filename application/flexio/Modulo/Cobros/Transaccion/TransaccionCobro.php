<?php
namespace Flexio\Modulo\Cobros\Transaccion;
use Flexio\Strategy\Transacciones\InterfaceTransaccion;
use Flexio\Repository\SysTransaccion\SysTransaccionRepository as SysTransaccion;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable;
use Illuminate\Database\Capsule\Manager as Capsule;

class TransaccionCobro implements InterfaceTransaccion{

  function hacerTransaccion($modelo){

    $modelo->load('cobros_facturas','metodo_cobro');
    $encontrar = $modelo->sistema_transaccion->count();
    //nueva instancia de SysTransaccion
    if($encontrar == 0){
    $sysTransaccion = new SysTransaccion;
    //datos de SysTransaccion
    $infoSysTransaccion = ['codigo'=>'Sys', 'nombre'=>'TransaccionCobro'.'-'.$modelo->codigo.'-'.$modelo->empresa_id,
    'empresa_id'=>$modelo->empresa_id,'linkable_id'=>$modelo->id,'linkable_type'=> get_class($modelo)];

    Capsule::transaction(function() use($modelo, $sysTransaccion, $infoSysTransaccion) {
      $modeloSysTransaccion =  $sysTransaccion->create($infoSysTransaccion);
      $modeloSysTransaccion->transaccion()->saveMany($this->transacciones($modelo));

      if(is_null($modeloSysTransaccion)){
        throw new \Exception('No se pudo hacer la transacción');
      }

    });
    }
  }

  function transacciones($modelo){
    return array_merge($this->debito($modelo),$this->acredito($modelo));
  }


  function debito($modelo){

    $asientos = [];
    if($modelo->depositable_type == "Flexio\Modulo\Contabilidad\Models\Cuentas"){
      $modelo_cuenta_id = $modelo->depositable_id;
    }else{
      $modelo_cuenta_id = $modelo->empresa->cuenta_caja_menuda->cuenta_id;
    }


    foreach($modelo->cobros_facturas->where('transaccion',0) as $cobro_factura){
      $factura = $cobro_factura->facturas;

      foreach($modelo->metodo_cobro as $metodo){
        if($metodo->tipo_pago != 'credito_favor'){
          $asientos[] = new AsientoContable(['codigo'=>$modelo->codigo,'nombre'=>$modelo->codigo. '-'.$factura->codigo,'debito'=>$metodo->total_pagado,'cuenta_id'=>$modelo_cuenta_id,'empresa_id'=>$modelo->empresa_id]);
        }else{
          $cuenta_id =  $modelo->empresa->cuentas_abonar_clientes->first()->cuenta_id;
          $asientos[] = new AsientoContable(['codigo'=>$modelo->codigo,'nombre'=>$modelo->codigo. '-'.$factura->codigo,'debito'=>$metodo->total_pagado,'cuenta_id'=>$cuenta_id,'empresa_id'=>$modelo->empresa_id]);
        }
      }

    }

    return $asientos;

  }

  function acredito($modelo){
    $asientos = array();
    $cuenta_id = $modelo->empresa->cuenta_por_cobrar->cuenta_id;

    foreach($modelo->cobros_facturas->where('transaccion',0) as $cobro_factura){
      $factura = $cobro_factura->facturas;
      $asientos[] = new AsientoContable(['codigo'=>$modelo->codigo,'nombre'=>$modelo->codigo. '-'.$factura->codigo,
      'credito'=> $cobro_factura->monto_pagado,'cuenta_id'=>$cuenta_id,'empresa_id'=>$modelo->empresa_id]);
      $cobro_factura->transaccion = 1;
      $cobro_factura->save();
    }

    return $asientos;

  }

}
