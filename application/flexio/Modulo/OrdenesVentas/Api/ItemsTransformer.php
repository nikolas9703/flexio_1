<?php
namespace Flexio\Modulo\OrdenesVentas\Api;
use Flexio\Transformers\TransformerObject;
use Flexio\Modulo\Cotizaciones\Models\LineItem;

class ItemsTransformer extends TransformerObject{

    public function transform($item)
    {
    
        return [
            'categoria_id'=> $item->categoria_id,
            'item_id'=> $item->item_id,
            'nombre' => $item->item->nombre,
            'cantidad' => (float)$item->cantidad,
            'unidad_id' =>  $item->unidad_id,
            'precio_unidad'=> (float)$item->precio_unidad,
            'impuesto_id' => $item->impuesto_id,
            'descuento' => $item->descuento,
            'cuenta_id' => $item->cuenta_id,
            'precio_total' => (float)$item->precio_total,
            'total_impuesto' => (float)$item->impuesto_total,
            'total_descuento'=> (float) $item->descuento_total,
            'comentario' => $item->comentario,
            'atributo_id' => $item->atributo_id,
            'atributo_text' => $item->atributo_text,
            'atributos' => $item->item->atributos,
            'impuesto'=> $item->impuesto->impuesto,
            'unidades'=> $item->item->unidades->map(function($unidad){
                    return [
                        'id'=> $unidad->id,
                        'nombre' => $unidad->nombre,
                        'base'=> $unidad->pivot->base,
                        'factor_conversion'=> $unidad->pivot->factor_conversion
                    ];
            }),
            "precios" => $item->item->precios->map(function($precio){
                    return[
                        'id' => $precio->id,
                        'nombre' => $precio->nombre,
                        'precio' => $precio->pivot->precio
                    ];
                })
        ];
    }

}
