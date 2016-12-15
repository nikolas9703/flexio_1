<?php
namespace Flexio\Modulo\Refactura\Events;

class FacturableEvent{
  protected $factura;
  protected $facturas_compras;
  function __construct($modelFactura, $facturas_compras)
  {
    $this->factura = $modelFactura;
    $this->facturas_compras = $facturas_compras;
  }

  function refacturar(){
      $empresa_id =  $this->factura->empresa_id;
      $this->factura->refactura()
      ->saveMany($this->facturas_compras,['empresa_id'=> $empresa_id]);
  }
}
