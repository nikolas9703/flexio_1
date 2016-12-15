<?php
namespace Flexio\Modulo\Cobros\Repository;
use Flexio\Modulo\Cobros\Models\MetodoCobro as MetodoCobro;

class MetodoCobroRepository{

  //return (new static)->handle();
  public static function formato($elementos){
    $metodo = new MetodoCobro;
    $metodo->tipo_pago = $elementos['tipo_pago'];
    $metodo->total_pagado = $elementos['total_pagado'];
    $metodo->referencia = $elementos['referencia'];
    return $metodo;
  }

}
