<?php
namespace Flexio\Modulo\Anticipos\Events;
use Flexio\Modulo\Anticipos\Transacciones\AnticipoTransaccion;
use Flexio\Modulo\Anticipos\Models\Anticipo;


class RealizarTransaccionAnticipo{

  public $anticipo;

  public function __construct(Anticipo $anticipo){

    $this->anticipo = $anticipo;
  }

  public function hacer(){

    $transaccion = new AnticipoTransaccion;
    $anticipo = $this->anticipo;
    $transaccion->hacerTransaccion($anticipo);
  }

}
