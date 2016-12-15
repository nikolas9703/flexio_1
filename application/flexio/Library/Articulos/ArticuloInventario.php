<?php

namespace Flexio\Library\Articulos;

class ArticuloInventario{

    public function get($articulos, $padre = NULL){

        return $articulos->map(function($line_item){
            return array_merge(
                $line_item->toArray(),
                [
                    'tipo_id' => $line_item->item->tipo_id,
                    'descripcion' => $line_item->item->descripcion,
                    'facturado' => true,
                    'atributos' => $line_item->item->atributos,
                    'item_hidden_id' => $line_item->item_id,
                    'unidad_hidden_id' => $line_item->unidad_id,
                    'items' => [],
                    'unidades' => [],
                    'precios' => [],
                    "cuentas" => $line_item->item->cuentas,
                    "seriales" => count($line_item->seriales) ? $line_item->seriales->map(function($serial){return ['nombre' => $serial->nombre];}) : []
                ]
            );
        });

    }

}
