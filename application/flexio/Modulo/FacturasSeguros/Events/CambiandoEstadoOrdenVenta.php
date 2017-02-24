<?php
namespace Flexio\Modulo\FacturasSeguros\Events;

class CambiandoEstadoOrdenVenta{
  protected $factura;
  protected $ordenVenta;
  function __construct($modelOrdenVenta)
  {
    $this->ordenVenta = $modelOrdenVenta;
  }

  function cambiarEstado(){

    $ordenVenta = $this->ordenVenta;

    $total = $this->total_items_factura($ordenVenta);
    $total_items_orden_venta = $ordenVenta->items->count();

    if($total_items_orden_venta > $total){
      $this->set_estado_orden($ordenVenta, 'facturado_parcial');
      return true;
    }
    $this->set_estado_orden($ordenVenta);
  }

   function total_items_factura($ordenVenta){
    $facturas = $ordenVenta->facturas;
    $total = 0;
    foreach($facturas as $fac){
      $total += $fac->items->count();
    }
    return $total;
  }

  function set_estado_orden($ordenVenta, $estado = 'facturado_completo'){
    $ordenVenta->estado = $estado;
    $ordenVenta->save();
  }
  
}
