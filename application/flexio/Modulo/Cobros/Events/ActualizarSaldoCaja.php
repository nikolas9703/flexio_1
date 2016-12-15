<?php

namespace Flexio\Modulo\Cobros\Events;

use Flexio\Modulo\Cajas\Models\Cajas;

class ActualizarSaldoCaja{

  public static function nuevoSaldo($caja_id, $monto){

    $caja = Cajas::find($caja_id);

    $caja->saldo =  $caja->saldo + $monto;
    $caja->save();
  }
}
