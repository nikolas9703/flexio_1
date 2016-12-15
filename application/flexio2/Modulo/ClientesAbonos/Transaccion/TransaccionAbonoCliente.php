<?php
namespace Flexio\Modulo\ClientesAbonos\Transaccion;
use Flexio\Strategy\Transacciones\InterfaceTransaccion;
use Flexio\Repository\SysTransaccion\SysTransaccionRepository as SysTransaccion;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable;
use Illuminate\Database\Capsule\Manager as Capsule;

class TransaccionAbonoCliente implements InterfaceTransaccion{

  function hacerTransaccion($modelo){
    $modelo->load('metodo_abono');

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
    $asientos = [];
    $cuenta_id = $modelo->cuenta_id;
    //activo
      $asientos[] = new AsientoContable(['codigo'=>$modelo->codigo,'nombre'=>$modelo->codigo. '-'.$modelo->cliente->nombre,'debito'=>$modelo->monto_abonado,'cuenta_id'=>$cuenta_id,'empresa_id'=>$modelo->empresa_id]);


    return $asientos;
  }
    function acredito($modelo){
      $asientos = array();
      $cuenta_id = $modelo->empresa->cuentas_abonar_clientes()->cuenta_id;
      //pasivo
    //  foreach($modelo->metodo_abono as $abono){

        $asientos[] = new AsientoContable(['codigo'=>$modelo->codigo,'nombre'=>$modelo->codigo. '-'.$modelo->cliente->nombre,
        'credito'=> $modelo->monto_abonado,'cuenta_id'=>$cuenta_id,'empresa_id'=>$modelo->empresa_id]);
  //}

      return $asientos;

    }


}
