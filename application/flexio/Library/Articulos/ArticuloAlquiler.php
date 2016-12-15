<?php

namespace Flexio\Library\Articulos;

class ArticuloAlquiler{

    public function get($articulos, $padre = NULL){

        return $articulos->map(function($contrato_item){
            return array_merge(
                $contrato_item->toArray(),
                [
                    "atributos" => $contrato_item->item->atributos,
                    "items" => [],
                    "item_hidden" => $contrato_item->item->id,
                    "tipo_id" => $contrato_item->item->tipo_id,// 7 => De servicio -> no muestra labels entregado, devueltos y en alquiler
                    "facturado" => true,
                    "cuentas" => $contrato_item->item->cuentas
                ]
            );
        });

    }

}
