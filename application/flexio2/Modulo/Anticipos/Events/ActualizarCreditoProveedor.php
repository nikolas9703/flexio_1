<?php
namespace Flexio\Modulo\Anticipos\Events;
use Flexio\Modulo\Proveedores\Models\Proveedores;
use Flexio\Modulo\Anticipos\Models\Anticipo;

class ActualizarCreditoProveedor{

  public $anticipo;

  public function __construct(Anticipo $anticipo){

    $this->anticipo = $anticipo;
  }

  public function hacer(){

    $anticipo = $this->anticipo;
    $proveedor = Proveedores::find($anticipo->anticipable_id);
    $proveedor->credito =  $proveedor->credito + $anticipo->monto;
    $proveedor->save();
  }
}
