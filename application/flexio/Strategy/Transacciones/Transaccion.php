<?php
namespace Flexio\Strategy\Transacciones;

class Transaccion{

  public function hacerTransaccion($modelo, InterfaceTransaccion $transaccion){

    $transaccion->hacerTransaccion($modelo);
    
  }

}
