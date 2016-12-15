<?php

namespace Flexio\Modulo\Anticipos\Transacciones;
use Flexio\Strategy\Transacciones\InterfaceTransaccion;
use Flexio\Modulo\Transaccion\Models\SysTransaccion;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable;
use Illuminate\Database\Capsule\Manager as Capsule;

class AnticipoTransaccion implements InterfaceTransaccion{


  function hacerTransaccion($modelo){

    //datos de SysTransaccion
    $infoSysTransaccion = ['codigo'=>'Sys', 'nombre'=>'TransaccionAnticipo'.'-'.$modelo->codigo.'-'.$modelo->empresa_id,
    'empresa_id'=>$modelo->empresa_id,'linkable_id'=>$modelo->id,'linkable_type'=> get_class($modelo)];
    //realiza la transaccion del sistema
    Capsule::transaction(function() use($modelo, $infoSysTransaccion) {
      $modeloSysTransaccion =  SysTransaccion::create($infoSysTransaccion);
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
    $cuenta_id  = $this->_getCuentaIdDebito($modelo);
    $asientos   = [];

        $asientos[] = new AsientoContable([
            'codigo'        => $modelo->codigo,
            'nombre'        => $modelo->codigo. ' - '.$modelo->anticipable->nombre,
            'debito'        => $modelo->monto,
            'cuenta_id'     => $cuenta_id,
            'empresa_id'    => $modelo->empresa_id
        ]);

        return $asientos;
  }

  function acredito($modelo){

    $cuenta_id  = $this->_getCuentaIdCredito($modelo);
    $asientos   = [];

    $asientos[] = new AsientoContable([
        'codigo'        => $modelo->codigo,
        'nombre'        => $modelo->codigo. " - " .$modelo->anticipable->nombre,
        'credito'       => $modelo->monto,
        'cuenta_id'     => $cuenta_id,
        'empresa_id'    => $modelo->empresa_id
    ]);

    return $asientos;
  }


  private function _getCuentaIdDebito($abono)
  {
    return  $abono->empresa->cuenta_abonar_proveedores->first()->cuenta_id;
  }

  private function _getCuentaIdCredito($abono)
  {
    return $abono->depositable_id;
  }

}
