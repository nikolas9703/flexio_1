<?php
namespace Flexio\Modulo\Cotizaciones\Models;


class CotizacionFractal{

  public function modelo($cotizacion)
  {
  	    return [
  	        'codigo'   => $cotizacion['codigo'],
  	        'cliente_id'    =>  $cotizacion['cliente_id'],
  	        'fecha_desde'    =>  $cotizacion['fecha_desde'],
  	        'fecha_hasta'    =>  $cotizacion['fecha_hasta'],
  	        'estado'    =>  $cotizacion['estado'],
  	        'creado_por'    =>  $cotizacion['creado_por'],
  	        'empresa_id'    =>  $cotizacion['empresa_id'],
  	        'comentario'    =>  $cotizacion['comentario'],
  	        'termino_pago'    =>  $cotizacion['termino_pago'],
  	        'fecha_termino_pago'    =>  $cotizacion['fecha_termino_pago'],
  	        'item_precio_id'    =>  $cotizacion['item_precio_id'],
  	        'subtotal'    =>  $cotizacion['subtotal'],
  	        'impuestos'    =>  $cotizacion['impuesto'],
  	        'descuento'    =>  $cotizacion['descuento'],
  	        'total'    =>  $cotizacion['total'],
  	    ];
  	}

    public function modelo($attributes){
      $resource = new Fractal\Resource\Item($cotizacion, function(Cotizacion $cotizacion) {});
    }



}
