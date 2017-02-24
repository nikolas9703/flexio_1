<?php
namespace Flexio\Modulo\OrdenesAlquiler\Api;
use Flexio\Transformers\TransformerObject;

class OrdenesAlquilerDetalle extends TransformerObject{

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
            'lista_precio_alquiler_id' => $ordenVenta->lista_precio_alquiler_id,
            'centro_contable_id' => $ordenVenta->centro_contable_id,
            'centro_facturacion_id' => $ordenVenta->centro_facturacion_id,
            'estado' => $ordenVenta->estado,
            'items'=> $this->includeItem($ordenVenta),
            'items_alquiler' => $this->includeItemAlquiler($ordenVenta)
	    ];
	}

    public function includeItem($ordenVenta)
    {
        $items = $ordenVenta->items->where("item_adicional", 1);
        return (new ItemsTransformer)->transformCollection($items);
    }

    public function includeItemAlquiler($ordenVenta)
    {
        $items = $ordenVenta->items->where("item_adicional", 0);
        $items = (new ItemsAlquilerTransformer)->transformCollection($items);
        return collect(array_values(collect($items)->toArray()));
    }

}
