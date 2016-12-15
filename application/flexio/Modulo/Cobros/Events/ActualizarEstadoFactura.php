<?php

namespace Flexio\Modulo\Cobros\Events;

use Flexio\Modulo\FacturasVentas\Models\FacturaVenta;

class ActualizarEstadoFactura{

  public static function manupilarEstado($facturaId){

    $facturas= FacturaVenta::whereIn('id',$facturaId)->get();

    $facturas->each(function($factura){
      if($factura->total_facturado() < $factura->total){
        (new ActualizarEstadoFactura)->setEstadoFactura($factura,'cobrado_parcial');
      }else if($factura->total_facturado() == $factura->total){
       (new ActualizarEstadoFactura)->setEstadoFactura($factura);
      }
    });

  }

  function setEstadoFactura($factura, $estado = 'cobrado_completo'){
    $factura->estado = $estado;
    $factura->save();
  }

}
