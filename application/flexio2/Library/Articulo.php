<?php

namespace Flexio\Library;

abstract class Articulo{

    public function get($articulos, $padre = NULL){

        return $articulos->map(function($item){
            return array_merge($item->pivot->toArray(),[
                'descripcion' => $item->descripcion,
                'facturado' => false,
                'atributos' => $item->atributos,
                'atributo_id' => '',
                'atributo_text' => '',
                'item_hidden_id' => $item->pivot->item_id,
                'unidad_hidden_id' => $item->pivot->unidad_id,
                'items' => [],
                'unidades' => [],
                "cuentas" => $item->cuentas
            ]);
        });

    }

    public function getEmpty(){

        return collect([
            [
                'id' => '',
                'cantidad' => '',
                'categoria_id' => '',
                'cuenta_id' => '',
                'descuento' => '',
                'impuesto_id' => '',
                'item_id' => '',
                'precio_total' => '',
                'precio_unidad' => '',
                'unidad_id' => '',
                'descripcion' => '',
                'facturado' => true,
                'atributos' => [],
                'atributo_id' => '',
                'atributo_text' => '',
                'item_hidden_id' => '',
                'unidad_hidden_id' => '',
                'items' => [],
                'unidades' => [],
                "cuentas" => "[]"
            ]
        ]);

    }

}
