<?php
namespace Flexio\Modulo\Anticipos\Events;
use Flexio\Modulo\Cliente\Models\Cliente;
use Flexio\Modulo\Anticipos\Models\Anticipo;

class ActualizarCreditoCliente{

  public $anticipo;

  public function __construct(Anticipo $anticipo){

    $this->anticipo = $anticipo;
  }

  public function hacer(){

    $anticipo = $this->anticipo;
    $cliente = Cliente::find($anticipo->anticipable_id);
    $cliente->credito_favor =  $cliente->credito_favor + $anticipo->monto;
    $cliente->save();
  }
}
