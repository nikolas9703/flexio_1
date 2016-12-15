<?php
namespace Flexio\Modulo\Devoluciones\Events;
use Flexio\Modulo\Entradas\Models\Entradas as Entradas;
class DevolucionEntregaEvent{
  protected $devolucion;

  function __construct($modelDevolucion)
  {
    $this->devolucion = $modelDevolucion;
  }

  function hacerEntrada(){
    if($this->devolucion->estado =='aprobada'){
      $datos = ['empresa_id'=> $this->devolucion->empresa_id,'estado_id'=> 1,'comentarios'=>'Entrada generada desde devolucion','codigo'=>Entradas::all()->count() + 1];
      $entrada = new Entradas($datos);
      $this->devolucion->entrada()
      ->save($entrada);
    }
  }

}
