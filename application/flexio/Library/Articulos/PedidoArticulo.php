<?php

namespace Flexio\Library\Articulos;

use Flexio\Library\Articulo;

class PedidoArticulo extends Articulo{

    public function get($articulos, $pedido = NULL){

        return $articulos->map(function($pedido_item){
            return [
                'categoria_id' => $pedido_item->categoria_id,
                'item_id' => $pedido_item->id_item,
                'cuenta_id' => $pedido_item->cuenta,
                'cantidad' => $pedido_item->cantidad,
                'unidad_id' => $pedido_item->unidad,
                'descripcion' => $pedido_item->item->descripcion,
                'precio_unidad' => $pedido_item->item->costo_promedio,
                'impuesto_id' => count($pedido_item->item->impuesto_compra) ? $pedido_item->item->impuesto_compra->id : '',// no es requerido en el detalle de item
                'atributos' => $pedido_item->item->atributos,
                'item_hidden_id' => $pedido_item->id_item,
                'unidad_hidden_id' => $pedido_item->unidad,
                'atributo_text' => $pedido_item->atributo_text,
                'atributo_id' => $pedido_item->atributo_id,
                'facturado' => true,
                'items' => [],
                'unidades' => [],
                "cuentas" => $pedido_item->item->cuentas

            ];
        });

    }

}
