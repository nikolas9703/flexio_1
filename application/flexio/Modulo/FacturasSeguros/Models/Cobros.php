<?php

namespace Flexio\Modulo\FacturasSeguros\Models;

class Cobros{

  protected $factura;

  function __construct($factura){
    $this->factura = $factura;
  }

  function cobrados(){
    return $this->factura->cobros()->where('cob_cobros.estado','aplicado');
  }
}
