<?php

namespace Flexio\Library\Articulos;

class ArticuloVenta{

    public function get($articulos, $padre = NULL){

        return $articulos->map(function($line_item){
            return array_merge(
                $line_item->toArray(),
                [
                    'descripcion' => !empty($line_item->item)?$line_item->item->descripcion:'',
                    'facturado' => true,
                    'atributos' => !empty($line_item->item)?$line_item->item->atributos:'',
                    'atributo_id' => $line_item->atributo_id,
                    'atributo_text' => $line_item->atributo_text,
                    'item_hidden_id' => $line_item->item_id,
                    'unidad_hidden_id' => $line_item->unidad_id,
                    'items' => [],
                    'unidades' => [],
                    'precios' => !empty($line_item->item)?$line_item->item->precios:'',
                    "cuentas" => !empty($line_item->item)?$line_item->item->cuentas:''
                ]
            );
        });

    }

}
