<?php

namespace Flexio\Library\Articulos;

use Flexio\Library\Articulo;
use Flexio\Library\Util\FlexioSession;

class PedidoArticulo extends Articulo{

    protected $FlexioSession;

    public function __construct()
    {
        $this->FlexioSession = new FlexioSession;
    }

    public function get($articulos, $pedido = NULL){

       $modulo = $this->FlexioSession->uri()->segment(1);
       $method = $this->FlexioSession->uri()->segment(2);
       if($modulo != 'pedidos'){
         $articulos = $articulos->filter(function ($value, $key) {
             $disponible = $value->cantidad - $value->cantidad_usada;
             return $disponible > 0;
         })->values();
       }

        return $articulos->map(function($pedido_item) use($modulo, $method){

            return [
                'categoria_id' => $pedido_item->categoria_id,
                'item_id' => $pedido_item->id_item,
                'cuenta_id' => $pedido_item->cuenta,
                'cuenta_codigo' => $pedido_item->cuenta_info->codigo,
                'cuenta_nombre' =>  $pedido_item->cuenta_info->nombre,
                'cantidad' =>($modulo == 'pedidos')?$pedido_item->cantidad:$pedido_item->cantidad-$pedido_item->cantidad_usada,
                'cantidad_disponible' =>($modulo == 'pedidos' && $method == 'ver')?$pedido_item->cantidad-$pedido_item->cantidad_usada:0,
                'unidad_id' => $pedido_item->unidad,
                'descripcion' => !is_null($pedido_item->item)?$pedido_item->item->descripcion:'',
                'precio_unidad' => !is_null($pedido_item->item)? $pedido_item->item->costo_promedio:0,
                'impuesto_id' => !is_null($pedido_item->item) ? $this->getImpuesto($pedido_item->item->impuesto_compra) : '',// no es requerido en el detalle de item
                'atributos' => !is_null($pedido_item->item)?$pedido_item->item->atributos:[],
                'item_hidden_id' => $pedido_item->id_item,
                'unidad_hidden_id' => $pedido_item->unidad,
                'atributo_text' => $pedido_item->atributo_text,
                'atributo_id' => $pedido_item->atributo_id,
                'facturado' => true,
                'items' => [],
                'unidades' => [],
                "cuentas" => !is_null($pedido_item->item)?$pedido_item->item->cuentas:[]

            ];
        });

    }

    function getImpuesto($impuesto){
           return  !is_null($impuesto)?$impuesto->id:'';
    }

}
