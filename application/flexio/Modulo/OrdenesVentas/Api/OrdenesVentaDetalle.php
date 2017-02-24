<?php
namespace Flexio\Modulo\OrdenesVentas\Api;
use Flexio\Transformers\TransformerObject;

class OrdenesVentaDetalle extends TransformerObject{

   protected $defaultIncludes = [
        'item'
    ];

    public function transform($ordenVenta)
	{
	    return [
	        'id'      => (int) $ordenVenta->id,
            'nombre'=> $ordenVenta->codigo .' - '.$ordenVenta->cliente_nombre,
	        'cliente_id'   =>(int) $ordenVenta->cliente_id,
	        'termino_pago'    =>  $ordenVenta->termino_pago,
            'fecha_desde'   => $ordenVenta->fecha_desde,
            'fecha_hasta' => $ordenVenta->fecha_hasta,
            'fecha_desde' => $ordenVenta->fecha_desde,
            'created_by' => $ordenVenta->created_by,
            'item_precio_id' => $ordenVenta->item_precio_id,
            'centro_contable_id' => $ordenVenta->centro_contable_id,
            'centro_facturacion_id' => $ordenVenta->centro_facturacion_id,
            'estado' => $ordenVenta->estado,
            'items'=> $this->includeItem($ordenVenta)
	    ];
	}

    public function includeItem($ordenVenta)
    {
        $items = $ordenVenta->items;
        return (new ItemsTransformer)->transformCollection($items);
    }

}
