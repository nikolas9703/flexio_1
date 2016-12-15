<?php

namespace Flexio\Library\Articulos;

use Flexio\Library\Articulo;

class OrdenCompraArticulo extends Articulo{

    public function get($articulos, $orden = NULL){

        return $articulos->map(function($item) use ($orden){
            return array_merge($item->pivot->toArray(),[
                'cantidad_maxima' => $item->pivot->cantidad,
                'descripcion' => $item->descripcion,
                'atributos' => $item->atributos,
                'item_hidden_id' => $item->pivot->item_id,
                'unidad_hidden_id' => $item->pivot->unidad_id,
                'facturado' => $this->_getFacturado($orden, $item),
                'items' => [],
                'unidades' => [],
                "cuentas" => $item->cuentas
            ]);
        });

    }

    private function _getFacturado($orden, $item){

        if($orden === NULL){

            return false;

        }

        return $orden->facturas->filter(function($factura) use ($item){

            return $factura->items->filter(function($factura_item) use ($item){

                return $item->id == $factura_item->id;

            })->count() > 0;

        })->count() ? true : false;

    }

}
