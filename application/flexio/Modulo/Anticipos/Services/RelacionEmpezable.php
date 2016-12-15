<?php
namespace Flexio\Modulo\Anticipos\Services;

abstract class RelacionEmpezable{

  private $scope;
  private $relaciones =['orden_compra','subcontrato','orden_venta','contrato'];

  function __construct($anticipo){
    $this->scope = $anticipo;
  }

  function documento(){

    $relacion = $this->getRelacion();

    if(is_null($relacion)){
      return null;
    }

    foreach($relacion as $key=>$value){
      if(method_exists($this, $key)){
        return call_user_func_array([$this, $key], [$value]);
      }
    }
  }


  function getRelacion(){
    $modelo = null;
    foreach($this->relaciones as $relacion){
      if($this->scope->getRelationValue($relacion)->count() > 0){
         $modelo = [$relacion => $this->scope->getRelationValue($relacion)->first()];
      }
    }
    return $modelo;
  }

  abstract protected function orden_compra($orden_compra);
  abstract protected function orden_venta($orden_venta);
  abstract protected function subcontrato($subcontrato);
  abstract protected function contrato($contrato);
}
