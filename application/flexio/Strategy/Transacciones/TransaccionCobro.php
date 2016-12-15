<?php
namespace Flexio\Strategy\Transacciones;
use Flexio\Repository\SysTransaccion\SysTransaccionRepository as SysTransaccionRepository;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable;
use Illuminate\Database\Capsule\Manager as Capsule;


class TransaccionCobro implements InterfaceTransaccion{

  function hacerTransaccion($modelo){

    $modelo->load('cobros_facturas','metodo_cobro');

    //nueva instancia de SysTransaccion
    $sysTransaccion = new SysTransaccionRepository;
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

  function transacciones($modelo){
    return array_merge($this->debito($modelo),$this->acredito($modelo));
  }

  function debito($modelo){
    $asientos = [];
    $cuenta_id = $modelo->cuenta_id;

    foreach($modelo->cobros_facturas->where('transaccion',0) as $cobro_factura){
      $factura = $cobro_factura->facturas;

      $asientos[] = new AsientoContable(['codigo'=>$modelo->codigo,'nombre'=>$modelo->codigo. '-'.$factura->codigo,'debito'=>$cobro_factura->monto_pagado,'cuenta_id'=>$cuenta_id,'empresa_id'=>$modelo->empresa_id]);
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
