<?php
namespace Flexio\Strategy\Transacciones\Anular;

class AnularTransaccion{

  function anular($modelo, InterfaceAnular $transaccion){
    $transaccion->deshacerTransaccion($modelo);
  }
}
