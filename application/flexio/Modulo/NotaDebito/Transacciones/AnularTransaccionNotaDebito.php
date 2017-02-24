<?php
namespace Flexio\Modulo\NotaDebito\Transacciones;
use Flexio\Strategy\Transacciones\Anular\InterfaceAnular;


class AnularTransaccionNotaDebito Implements InterfaceAnular{

  function deshacerTransaccion($modelo){
    $modelo->load('sistema_transaccion.transaccion');

    $this->eliminarTransaccion($modelo);

  }

  private function eliminarTransaccion($modelo){
    foreach($modelo->sistema_transaccion as $sysTransaccion){
          foreach($sysTransaccion->transaccion as $transaccion){
            $transaccion->delete();
          }
        $sysTransaccion->delete();
      }
  }
}
