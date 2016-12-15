<?php
namespace Flexio\Modulo\NotaCredito\Transaccion;
use Flexio\Repository\SysTransaccion\SysTransaccionRepository as SysTransaccion;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Strategy\Transacciones\InterfaceTransaccion;


class TransaccionNotaCredito implements InterfaceTransaccion{

  function hacerTransaccion($modelo){
    $modelo->load('items');

    //nueva instancia de SysTransaccion
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


  function transacciones($modelo){
    return array_merge($this->debito($modelo),$this->acredito($modelo));
  }

  // se debita cada linea de la nota de credito
  // se debita con su respectivo impuesto
  function debito($modelo){
    $asientos = [];

    foreach($modelo->items as $nota_credito_item){
      $monto = $nota_credito_item->monto;
      //item de la nota de credito
      $asientos[] = new AsientoContable(['codigo'=>$modelo->codigo,'nombre'=>$modelo->codigo,
      'debito'=>$monto,'cuenta_id'=>$nota_credito_item->cuenta_id,'empresa_id'=>$modelo->empresa_id]);

      //impuesto del items de la nota de credito
      $asientos[] = new AsientoContable(['codigo'=>$modelo->codigo,'nombre'=>$modelo->codigo." ".$nota_credito_item->impuesto->nombre,
      'debito'=>$nota_credito_item->impuesto_total,'cuenta_id'=>$nota_credito_item->impuesto->cuenta_id,'empresa_id'=>$modelo->empresa_id]);

    }

    return $asientos;
  }
  // se acredita el total de la nota de credito
  function acredito($modelo){

    $asientos = array();
    $cuenta_id = $modelo->empresa->cuenta_por_cobrar->cuenta_id;

      $asientos[] = new AsientoContable(['codigo'=>$modelo->codigo,'nombre'=>$modelo->codigo,
      'credito'=> $modelo->total,'cuenta_id'=>$cuenta_id,'empresa_id'=>$modelo->empresa_id]);


    return $asientos;
  }
}
