<?php
namespace Flexio\Modulo\Pedidos\Repository;
use Flexio\Modulo\Pedidos\Models\Pedidos as Pedido;
use Flexio\Modulo\Comentario\Models\Comentario;

class PedidoRepository{


    private function _filtros($query, $clause){

        if(isset($clause['empresa_id']) and !empty($clause['empresa_id'])){$query->whereIdEmpresa($clause['empresa_id']);}
        if(isset($clause['ordenables']) and $clause['ordenables']){$query->ordenables();}

    }

    public function get($clause = []){

        $pedidos = Pedido::where(function($query) use ($clause){

            $this->_filtros($query, $clause);

        });

        return $pedidos->get();

    }

    public function count($clause = []){

        $pedidos = Pedido::where(function($query) use ($clause){

            $this->_filtros($query, $clause);

        });

        return $pedidos->count();

    }

    public function _sync_items($pedido, $items){

        $pedido->pedidos_items()->whereNotIn('id',array_pluck($items,'id_pedido_item'))->delete();
        foreach ($items as $item) {

            $pedido_item_id = (isset($item['id_pedido_item']) and !empty($item['id_pedido_item'])) ? $item['id_pedido_item'] : '';
            $pedido_item = $pedido->pedidos_items()->firstOrNew(['id'=>$pedido_item_id]);
            $pedido_item->categoria_id = $item['categoria'];
            $pedido_item->id_item = $item['item_id'];
            $pedido_item->cantidad = $item['cantidad'];
            $pedido_item->unidad = $item['unidad'];
            $pedido_item->cuenta = $item['cuenta'];
            $pedido_item->comentario = isset($item['comentario']) ? $item['comentario'] : '';
            $pedido_item->atributo_id = isset($item['atributo_id']) ? $item['atributo_id'] : '';
            $pedido_item->atributo_text = isset($item['atributo_text']) ? $item['atributo_text'] : '';
            $pedido_item->save();

        }

    }

    public function create($created) {

        $pedido = Pedido::create($created['campo']);
        $this->_sync_items($pedido, $created['items']);

        return $pedido;
    }

    public function update($update) {

        $pedido = Pedido::find($update['campo']['id']);
        $pedido->update($update['campo']);
        $this->_sync_items($pedido, $update['items']);

        return $pedido;
    }

    public function getCollectionPedidos($pedidos){

        return $pedidos->map(function($pedido){
            $articulo = new \Flexio\Library\Articulos\PedidoArticulo;
            $estado = count($pedido->estado) ? ' - '.$pedido->estado->etiqueta : '';
            $centro = count($pedido->centro_contable) ? ' - '.$pedido->centro_contable->nombre : '';
            return [
                //se usan para el catalogo de pedidos
                'id' => $pedido->id,
                'nombre' => 'PD'.$pedido->numero.$estado.$centro,
                //se usan para el detalle de la orden de compra
                'centro_contable_id' => $pedido->uuid_centro,
                'recibir_en_id' => $pedido->uuid_lugar,
                'proveedor_id' => '',
                'referencia' => $pedido->referencia,
                'terminos_pago' => '',
                'articulos' => $articulo->get($pedido->pedidos_items, $pedido)
            ];
        });

    }

    public function getCollectionPedido($pedido){

        $articulo = new \Flexio\Library\Articulos\PedidoArticulo;

        return Collect(
                array_merge(

                        $pedido->toArray(),

                        [
                            'articulos' => $articulo->get($pedido->pedidos_items, $pedido),
                            'estado' => $pedido->id_estado,
                            'comentario' => $pedido->comentario
                        ]

                ));

    }


  function find($id)
  {
    return Pedido::find($id);
  }

  function findByUuid($uuid){
  	return Pedido::where('uuid_pedido',hex2bin($uuid))->first();
  }
  function agregarComentario($ordenId, $comentarios){
   	$pedido = Pedido::find($ordenId);


  	$comentario = new Comentario($comentarios);

   $pedido->comentario()->save($comentario);

  	return $pedido;
  }



}
