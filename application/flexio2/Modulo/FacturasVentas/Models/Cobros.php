<?php

namespace Flexio\Modulo\FacturasVentas\Models;

class Cobros{

  protected $factura;

  function __construct($factura){
    $this->factura = $factura;
  }

  function cobrados(){
    return $this->factura->cobros()->where('cob_cobros.estado','aplicado');
  }
}
