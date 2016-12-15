<?php
namespace Flexio\Modulo\Anticipos\Services;

class TipoDocumento extends RelacionEmpezable{

  function __construct($anticipo){
    parent::__construct($anticipo);
  }

  function orden_compra($orden_compra){
    return $orden_compra->numero;
  }

  function orden_venta($orden_venta){
    return $orden_venta->codigo;
  }

  function subcontrato($subcontrato){
    return $subcontrato->codigo;
  }

  function contrato($contrato){
    return $contrato->codigo;
  }

}
