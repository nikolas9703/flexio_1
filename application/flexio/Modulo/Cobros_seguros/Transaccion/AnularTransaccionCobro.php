<?php
namespace Flexio\Modulo\Cobros_seguros\Transaccion;
use Flexio\Strategy\Transacciones\Anular\InterfaceAnular;
use Flexio\Repository\SysTransaccion\SysTransaccionRepository as SysTransaccion;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable;


class AnularTransaccionCobro Implements InterfaceAnular{

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
