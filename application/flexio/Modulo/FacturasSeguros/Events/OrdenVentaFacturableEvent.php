<?php
namespace Flexio\Modulo\FacturasSeguros\Events;

class OrdenVentaFacturableEvent{
  protected $factura;
  protected $ordenVenta;
  function __construct($modelFactura, $modelOrdenVenta)
  {
    $this->factura = $modelFactura;
    $this->ordenVenta = $modelOrdenVenta;
  }

  function relacionOrdenVenta(){
      $this->factura->ordenes_ventas()
      ->save($this->ordenVenta,['empresa_id'=> $this->ordenVenta->empresa_id]);
  }
}
