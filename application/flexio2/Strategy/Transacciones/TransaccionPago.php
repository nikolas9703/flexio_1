<?php
namespace Flexio\Strategy\Transacciones;
use Flexio\Modulo\ConfiguracionContabilidad\Models\CuentaPorPagar;
use Flexio\Repository\SysTransaccion\SysTransaccionRepository as SysTransaccionRepository;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable as Transaccion_orm;
use Illuminate\Database\Capsule\Manager as Capsule;
// cargar el modelo de codeigniter
class TransaccionPago implements InterfaceTransaccion{

  function hacerTransaccion($modelo){

    $encontrar = SysTransaccionRepository::findByNombre('TransaccionPago'.'-'.$modelo->codigo.'-'.$modelo->empresa_id);

    if($encontrar == 0){
      $sysTransaccion = new SysTransaccionRepository;
      $modeloSysTransaccion="";
      $infoSysTransaccion = array('codigo'=>'Sys', 'nombre'=>'TransaccionPago'.'-'.$modelo->codigo.'-'.$modelo->empresa_id,'empresa_id'=>$modelo->empresa_id);
      Capsule::transaction(function() use($sysTransaccion, $modeloSysTransaccion, $infoSysTransaccion, $modelo){
          $modeloSysTransaccion =  $sysTransaccion->create($infoSysTransaccion);
          $modeloSysTransaccion->transaccion()->saveMany($this->iteams_transacciones($modelo));
          if(is_null($modeloSysTransaccion)){
            throw new \Exception('No se pudo hacer la transacción');
          }
      });
    }
  }

  function iteams_transacciones($modelo){

      $asientos = array();
    //datos del pago
    $asientos[] = new Transaccion_orm (
        array('codigo'=>$modelo->codigo,'nombre'=>$modelo->codigo,'debito'=>$modelo->monto_pagado,'cuenta_id'=>$modelo->cuenta_id,'empresa_id'=>$modelo->empresa_id)
        );

      $asientos[] = new Transaccion_orm (
          array('codigo'=>$modelo->codigo,'nombre'=>$modelo->codigo,'credito'=>$modelo->monto_pagado,'cuenta_id'=>CuentaPorPagar::findOrNew(4)->cuenta_id,'empresa_id'=>$modelo->empresa_id)
      );

/*
      //datos de los items
    foreach ($modelo->items as  $value) {
        $itemInfo = $value->inventario_item;
      $atributesItem = array('nombre'=>$modelo->codigo.' '.$itemInfo->codigo,'credito'=> $value->precio_total ,'centro_id'=>$modelo->centro_contable_id,'cuenta_id'=>$value->cuenta_id,'empresa_id'=>$modelo->empresa_id);
      //transaccion de items
      $asientos[] = new Transaccion_orm($atributesItem);
      $impuesto = $value->impuesto->cuenta;
        dd($impuesto);
      //transaccion impuestos
     $atributesImpuesto = array('nombre'=>$impuesto->nombre, 'credito'=> $value->impuesto_total ,'centro_id'=> $modelo->centro_contable_id,'cuenta_id'=>$value->impuesto->cuenta_id,'empresa_id'=>$modelo->empresa_id);
      $asientos[] = new Transaccion_orm($atributesImpuesto);
    }
*/

    return $asientos;
  }

//estos metodos no se utilizan
  function items($articulos, $modelo){
    $asientos = null;
        $itemInfo = $articulos->inventario_item;
        $atributesItem = array('nombre'=>$modelo->codigo.' '.$itemInfo->codigo,'credito'=> $articulos->precio_total ,'centro_id'=>$modelo->centro_contable_id,'cuenta_id'=>$articulos->cuenta_id,'empresa_id'=>$modelo->empresa_id);
        $asientos = new Transaccion_orm($atributesItem);

    return $asientos;
  }

  function impuesto($item, $modelo){
    $asientos = null;
    $impuesto = $item->impuesto->cuenta;
    $atributesImpuesto = array('nombre'=>$impuesto[0]->nombre, 'credito'=> $item->impuesto_total ,'centro_id'=> $modelo->centro_contable_id,'cuenta_id'=>$impuesto[0]->id,'empresa_id'=>$modelo->empresa_id);
    $asientos  = new Transaccion_orm($atributesImpuesto);
  }

}
