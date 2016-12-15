<?php namespace Flexio\Modulo\Cotizaciones\Models;
use Carbon\Carbon as Carbon;
use Flexio\Transformers\Transformer;
class CotizacionTransformer extends Transformer{
  public function transform($cotizacion)
  {
    return [
        'codigo'   => $cotizacion['codigo'],
        'cliente_id'    =>  $cotizacion['cliente_id'],
        'fecha_desde'    => Carbon::createFromFormat('m/d/Y',$cotizacion['fecha_desde'],'America/Panama'),
        'fecha_hasta'    => Carbon::createFromFormat('m/d/Y',$cotizacion['fecha_hasta'],'America/Panama') ,
        'estado'    =>  $cotizacion['estado'],
        'creado_por'    =>  $cotizacion['creado_por'],
        'empresa_id'    =>  $cotizacion['empresa_id'],
        'comentario'    =>  $cotizacion['comentario'],
        'termino_pago'    =>  $cotizacion['termino_pago'],
        'item_precio_id'     =>  $cotizacion['item_precio_id'],
        'subtotal'    =>  $cotizacion['subtotal'],
        'impuestos'    =>  $cotizacion['impuesto'],
        'descuento'    =>  $cotizacion['descuento'],
        'total'    =>  $cotizacion['total'],
    ];
  }
}
