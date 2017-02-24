<?php
namespace Flexio\Modulo\Anticipos\Transacciones;

use Flexio\Strategy\Transacciones\Anular\InterfaceAnular;
use Flexio\Modulo\Transaccion\Models\SysTransaccion;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable;
use Illuminate\Database\Capsule\Manager as Capsule;

class AnticipoAnularTransaccion Implements InterfaceAnular{

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
