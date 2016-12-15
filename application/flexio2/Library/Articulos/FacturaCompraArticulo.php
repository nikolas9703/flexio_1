<?php

namespace Flexio\Library\Articulos;

use Flexio\Library\Articulo;

class FacturaCompraArticulo extends Articulo{

    public function get($articulos, $factura_compra = NULL){

        return $articulos->map(function($factura_item){
             return array_merge($factura_item->toArray(),[
                'cantidad_maxima' => $factura_item->cantidad,
                'descripcion' => $factura_item->item->descripcion,
                'atributos' => $factura_item->item->atributos,
                'item_hidden_id' => $factura_item->item_id,
                'unidad_hidden_id' => $factura_item->unidad_id,
                'facturado' => true,
                'items' => [],
                'unidades' => [],
                "cuentas" => $factura_item->item->cuentas
            ]);
        });

    }

}
