<?php
namespace Flexio\Modulo\Inventarios\Collections;


class ItemsVentas {

    public static function getCollectionVentas($items){
        $items = collect($items);

        return $items->map(function($item){

            return [
                "id" => $item->id,
                "nombre" => $item->nombre,
                "impuesto_id"  => count($item->impuesto_venta) ? $item->impuesto_venta->id : '',
                "cuenta_id" => $item->cuenta_ventas,
                "categoria"=> $item->categorias,
                'atributos'=> $item->atributos,
                "codigo"=> $item->codigo,
                'unidades' => $item->unidades->map(function($unidad){
                    return [
                        'id'=> $unidad->id,
                        'nombre' => $unidad->nombre,
                        'base'=> $unidad->pivot->base,
                        'factor_conversion'=> $unidad->pivot->factor_conversion
                    ];
                }),
                'unidad_id' => $item->unidad_id,
                "precios" => $item->precios->map(function($precio){
                    return[
                        'id' => $precio->id,
                        'nombre' => $precio->nombre,
                        'precio' => $precio->pivot->precio
                    ];
                })
                /*,
                "precios_alquiler" => $item->precios_alquiler->map(function($precio_alquiler){
                   return  [
                        "id_precio" => $precio_alquiler->pivot->id_precio,
                        "id_item" => $precio_alquiler->pivot->id_item,
                        "hora" => $precio_alquiler->pivot->hora,
                        "diario" => $precio_alquiler->pivot->diario,
                        "semanal" => $precio_alquiler->pivot->semanal,
                        "mensual" => $precio_alquiler->pivot->mensual,
                        "tarifa_4_horas" => $precio_alquiler->pivot->tarifa_4_horas,
                        "tarifa_6_dias" => $precio_alquiler->pivot->tarifa_6_dias,
                        "tarifa_15_dias" => $precio_alquiler->pivot->tarifa_15_dias,
                        "tarifa_28_dias" => $precio_alquiler->pivot->tarifa_28_dias,
                        "tarifa_30_dias" => $precio_alquiler->pivot->tarifa_30_dias
                    ];
                }),*/

            ];
        });
    }
}
