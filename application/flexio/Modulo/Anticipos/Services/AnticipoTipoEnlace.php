<?php
namespace Flexio\Modulo\Anticipos\Services;

class AnticipoTipoEnlace extends RelacionEmpezable{

  function __construct($anticipo){
    parent::__construct($anticipo);
  }

  function enlace(){
    return $this->documento();
  }

  function orden_compra($orden_compra){
    return $orden_compra->enlace;
  }

  function orden_venta($orden_venta){
    return $orden_venta->enlace;
  }

  function subcontrato($subcontrato){
    return $subcontrato->enlace;
  }

  function contrato($contrato){
    return $contrato->enlace;
  }

}
