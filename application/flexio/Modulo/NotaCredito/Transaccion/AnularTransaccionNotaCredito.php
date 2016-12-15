<?php
namespace Flexio\Modulo\NotaCredito\Transaccion;
use Flexio\Strategy\Transacciones\Anular\InterfaceAnular;
use Flexio\Repository\SysTransaccion\SysTransaccionRepository as SysTransaccion;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable;
use Illuminate\Database\Capsule\Manager as Capsule;

class AnularTransaccionNotaCredito implements InterfaceAnular{

  function deshacerTransaccion($modelo){
    //nueva instancia de SysTransaccion
    $sysTransaccion = new SysTransaccion;
    //datos de SysTransaccion
    $infoSysTransaccion = ['codigo'=>'Sys', 'nombre'=>'TransaccionAbonoCliente'.'-'.$modelo->codigo.'-'.$modelo->empresa_id,
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

    $asientos = array();
    $cuenta_id = $modelo->empresa->cuenta_por_cobrar->cuenta_id;

      $asientos[] = new AsientoContable(['codigo'=>$modelo->codigo,'nombre'=>$modelo->codigo,
      'debito'=> $modelo->total,'cuenta_id'=>$cuenta_id,'empresa_id'=>$modelo->empresa_id]);

    return $asientos;
  }

  function acredito($modelo){

    $asientos = [];

    foreach($modelo->items as $nota_credito_item){
      $monto = $nota_credito_item->monto;
      //items de la nota credito
      $asientos[] = new AsientoContable(['codigo'=>$modelo->codigo,'nombre'=>$modelo->codigo,
      'credito'=>$monto,'cuenta_id'=>$nota_credito_item->cuenta_id,'empresa_id'=>$modelo->empresa_id]);
      //impuesto de los items
      $asientos[] = new AsientoContable(['codigo'=>$modelo->codigo,'nombre'=>$modelo->codigo." ".$nota_credito_item->impuesto->nombre,
      'credito'=>$nota_credito_item->impuesto_total,'cuenta_id'=>$nota_credito_item->impuesto->cuenta_id,'empresa_id'=>$modelo->empresa_id]);

    }

    return $asientos;
  }
}
