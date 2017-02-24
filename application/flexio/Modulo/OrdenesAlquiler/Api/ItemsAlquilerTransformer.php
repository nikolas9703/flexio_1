<?php
namespace Flexio\Modulo\OrdenesAlquiler\Api;
use Flexio\Transformers\TransformerObject;
use Flexio\Modulo\Cotizaciones\Models\LineItem;

class ItemsAlquilerTransformer extends TransformerObject{

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
            'impuesto'=> is_null($item->impuesto)? 0 : $item->impuesto->impuesto,
            'tarifa_pactada'=> $item->tarifa_pactada,
            'tarifa_fecha_desde' => $item->tarifa_fecha_desde,
            'tarifa_fecha_hasta'=> $item->tarifa_fecha_hasta,
            'tarifa_monto' => $item->tarifa_monto,
            'tarifa_cantidad_periodo'=> $item->tarifa_cantidad_periodo,
            'tarifa_periodo_id' =>  $item->tarifa_periodo_id,
            'periodo' => $item->periodo
        ];
    }

}
