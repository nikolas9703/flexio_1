<?php

namespace Flexio\Library\Articulos;

use Flexio\Library\Articulo;

class OrdenCompraArticulo extends Articulo{

    public function get($articulos, $orden = NULL){

      return $articulos->map(function($item) use ($orden){
       $pedido_item_encontrado = [];
       $cantidad_disponible = 0;
       if(count($orden->pedido)>0){
         $pedido_item_encontrado =  $orden->pedido->pedidos_items->filter(function ($item_pedido) use ($item) {
               return $item_pedido->id_item == $item->pivot->item_id && $item_pedido->atributo_text == $item->pivot->atributo_text;
         })->first();
         $cantidad_disponible = count($pedido_item_encontrado) ? $pedido_item_encontrado->cantidad - $pedido_item_encontrado->cantidad_usada : 0;
       }

        return array_merge($item->pivot->toArray(),[
             'cantidad_maxima' => $item->pivot->cantidad,
             'cantidad_disponible' => $cantidad_disponible,
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
