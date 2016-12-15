<?php
namespace Flexio\Modulo\Anticipos\Events;
use Flexio\Modulo\Anticipos\Transacciones\AnticipoTransaccionVentas;
use Flexio\Modulo\Anticipos\Models\Anticipo;


class RealizarTransaccionAnticipoVentas{

  public $anticipo;

  public function __construct(Anticipo $anticipo){

    $this->anticipo = $anticipo;
  }

  public function hacer(){

    $transaccion = new AnticipoTransaccionVentas;
    $anticipo = $this->anticipo;
    $transaccion->hacerTransaccion($anticipo);
  }

}
