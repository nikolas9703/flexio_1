<?php
namespace Flexio\Modulo\FacturasVentas\Api;
use Flexio\Transformers\TransformerObject;

class FacturaVentaDetalle extends TransformerObject{

    public function transform($factura)
	{
	    return [
	        'id'      => (int) $factura->id,
            'nombre'=> $factura->codigo .' - '.$factura->cliente_nombre,
	        'cliente_id'   =>(int) $factura->cliente_id,
	        'termino_pago'    =>  $factura->termino_pago,
            'fecha_desde'   => $factura->fecha_desde,
            'fecha_hasta' => $factura->fecha_hasta,
            'created_by' => $factura->created_by,
            'item_precio_id' => $factura->item_precio_id,
            'lista_precio_alquiler_id' => !empty($factura->lista_precio_alquiler_id) ? $factura->lista_precio_alquiler_id : "",
            'centro_contable_id' => $factura->centro_contable_id,
            'centro_facturacion_id' => $factura->centro_facturacion_id,
            'estado' => $factura->estado,
            'items'=> $this->includeItem($factura),
            'items_alquiler' => $this->includeItemAlquiler($factura),
            'cobros' => is_null($factura->total_facturado())? 0 : $factura->total_facturado(),
            'empezable_type' => $factura->empezable_type,
            'empezable_id' => $factura->empezable_id,
            'comentario' => $factura->comentario
	    ];
	}

    public function includeItem($factura)
    {
        return (new \Flexio\Modulo\LineItems\Api\LineItemsTransformer)->transformCollection($factura->items_venta);
    }

    public function includeItemAlquiler($ordenVenta)
    {
        $items = $ordenVenta->items_alquiler;
        $items = (new \Flexio\Modulo\OrdenesAlquiler\Api\ItemsAlquilerTransformer)->transformCollection($items);
        return collect(array_values(collect($items)->toArray()));
    }
}
