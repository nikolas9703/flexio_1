<?php
namespace Flexio\Modulo\Pedidos\Repository;
use Flexio\Modulo\Pedidos\Models\Pedidos as Pedido;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\Politicas\Models\Politicas;
use Flexio\Modulo\Usuarios\Models\Usuarios;
use Flexio\Library\Util\FlexioSession;
use Illuminate\Database\Capsule\Manager as Capsule;

class PedidoRepository{

    public function __construct()
    {     
        $this->session = new FlexioSession();        
    }
    private function _filtros($query, $clause){

        if(isset($clause['empresa_id']) and !empty($clause['empresa_id'])){$query->whereIdEmpresa($clause['empresa_id']);}

    }

    public function get($clause = []){

        $pedidos = Pedido::where(function($query) use ($clause){

            $this->_filtros($query, $clause);

        });
        if(isset($clause['ordenables']) and $clause['ordenables']){$pedidos->ordenables();}

        return $pedidos->get();

    }

    public function count($clause = []){

        $pedidos = Pedido::where(function($query) use ($clause){

            $this->_filtros($query, $clause);

        });
        if(isset($clause['ordenables']) and $clause['ordenables']){$pedidos->ordenables();}

        return $pedidos->count();

    }
/*  public function actualizarEstado()
  {
      //items de la orden de compra -> solo id
      $ordenes_items = [];
      foreach ($this->items as $item) {
          $ordenes_items[$item->id] = isset($ordenes_items[$item->id]) ? $ordenes_items[$item->id] + $item->pivot->cantidad : $item->pivot->cantidad;
      }

      //obtengo las facturas asociadas a la orden de compra
      //facturas que no tengan el estado de aprobadas
      //facturas que no tengan el estado de anulada
      $facturas = Facturas_compras_orm::where("operacion_type", "Ordenes_orm")
          ->where("operacion_id", $this->id);

      //items de las facturas asociadas a la orden de compra
      $facturas_items = [];
      foreach ($facturas->get() as $factura) {
          if ($factura->valida) {
              foreach ($factura->facturas_compras_items as $factura_item) {
                  $facturas_items[$factura_item->item_id] = isset($facturas_items[$factura_item->item_id]) ? $facturas_items[$factura_item->item_id] + $factura_item->cantidad : $factura_item->cantidad;
              }
          }
      }
      // Se inicializa el valor a Por facturar si no tiene facturas asociadas
      //2.- Por facturar
      //3.- Orden facturada parcial
      //4.- Orden facturada completo
      $this->id_estado = count($facturas_items) > 0 ? 4 : 2;

      if (count($facturas_items) < count($ordenes_items)) {
          $this->id_estado = 3;
      } else {
          foreach ($ordenes_items as $key => $value) {
              if ((isset($ordenes_items[$key]) && isset($facturas_items[$key])) && doubleval($facturas_items[$key]) < doubleval($ordenes_items[$key])) {
                  $this->id_estado = 3;
                  break;
              }
          }
      }

      $this->save();
  }*/

  public function actualizarEstadoPedido($pedido, $orden_compra, $orden_original = NULL){

      $diferentes = [];
     foreach ($pedido->pedidos_items as $key => $item_orden) {
           $orden_item_nuevo =  $orden_compra->items->filter(function ($item, $key) use ($item_orden) {
               return $item->id == $item_orden->id_item && $item->pivot->atributo_text == $item_orden->atributo_text;
           })->first();


           if($orden_item_nuevo == null){
               if($item_orden->cantidad - $item_orden->cantidad_usada > 0) //Solo concideralo incompleto si todavia sigue disponible en pedidos el item
                $diferentes[] = 1;
           }else{
              if($orden_original != NULL ){
                     $orden_item_original_encontrado =  $orden_original->items->filter(function ($original)  use ($item_orden)  {
                         return $original->id == $item_orden->id_item && $original->pivot->atributo_text == $item_orden->atributo_text;
                     })->first();

                     if($orden_original->id_estado == 1){
                       //$orden_item_nuevo->pivot->cantidad; //Cantidad del forulario en la orden

                       $operacion1 = $item_orden->cantidad_usada + $orden_item_nuevo->pivot->cantidad; //Nueva cantidad Usada
                       $item_orden->cantidad_usada = $operacion1;
                       if($operacion1 != $item_orden->cantidad){
                          $diferentes[] = 1;
                       }
                     }
                    else if($orden_item_original_encontrado->pivot->cantidad != $orden_item_nuevo->pivot->cantidad) {

                       $diferentes[] = 1;
                       //$orden_item_nuevo->pivot->cantidad; //Cantidad del forulario en la orden
                       $operacion1 = $item_orden->cantidad_usada - $orden_item_original_encontrado->pivot->cantidad;
                       $operacion1 = $operacion1 + $orden_item_nuevo->pivot->cantidad;
                       $item_orden->cantidad_usada = $operacion1;
                     }
             }else{

               $item_orden->cantidad_usada = $item_orden->cantidad_usada + $orden_item_nuevo->pivot->cantidad;
               if($item_orden->cantidad_usada<  $item_orden->cantidad ){
                 $diferentes[] = 1;
              }
             }
               $item_orden->save();
           }
     }
     $this->_cambiandoEstadoPedido($pedido, count($diferentes));
   }
   public function _cambiandoEstadoPedido($pedido, $diferentes){
         if( $diferentes == 0)
           $pedido->id_estado = 4; //procesada
         else
           $pedido->id_estado = 3; //Parcial
   return   $pedido->save();
 }
    /*public function actualizarEstadoPedido($pedido, $orden_compra){

      $diferentes = [];
      foreach ($pedido->pedidos_items as $key => $item_orden) {

            $orden_item_encontrado =  $orden_compra->items->filter(function ($item, $key) use ($item_orden) {
                return $item->id == $item_orden->id_item && $item->pivot->atributo_text == $item_orden->atributo_text;
            })->first();
            if($orden_item_encontrado == null){
                 $diferentes[] = 1;
            }
            else if($orden_item_encontrado->pivot->cantidad != $item_orden->cantidad){
                 $diferentes[] = 1;
            }
      }
      $this->_cambiandoEstadoPedido($pedido, count($diferentes));

    }*/


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

    public function guardarEstado($id, $estado_id, $empresa_id) {
       /* if(is_array($id)){
        $campos['empresa_id'] = $empresa_id;
        $campos['estado_id'] = $estado_id; 
        foreach($id AS $info){
        $pedido = Pedido::find($info);           
        if(!count($this->getPoliticas($pedido, $campos)))throw new \Exception("No tiene permisos para cambiar el estado del Pedido Nro. {$pedido->codigo}");        
        //inicia la validacion de politicas de transaccion
        //$politicas = $this->politicas        
        $pedido->id_estado = $campos['estado_id'];       
        $pedido->update();
        return $pedido;
        }
        }else{          */    
        $pedido = Pedido::find($id);
        $campos['empresa_id'] = $empresa_id;
        $campos['estado_id'] = $estado_id;       
         if(!count($this->getPoliticas($pedido, $campos)))throw new \Exception("No tiene permisos para cambiar el estado del Pedido Nro. {$pedido->codigo}");    
         if($pedido->id_estado != '1')throw new \Exception("El pedido Nro. {$pedido->codigo} requiere estar 'Por aprobar' antes de cambiar el estado");   
        //inicia la validacion de politicas de transaccion
        //$politicas = $this->politicas        
        $pedido->id_estado = $campos['estado_id'];       
        $pedido->update();
        return $pedido;
   // }
    }

    //politicas
    private function getPoliticas($pedido, $campos)
    {
        $usuario = Usuarios::find($this->session->usuarioId());
        $campos['role_id'] = count($usuario->roles_reales->first()) ? $usuario->roles_reales->first()->id : -1;
        $campos['categorias'] = count($pedido->pedidos_items) ? $pedido->pedidos_items->pluck('categoria_id') : [-1];
        return Politicas::select('ptr_transacciones.*')->where(function($q) use ($pedido, $campos){
            $q->where('ptr_transacciones.empresa_id', $campos['empresa_id']);
            $q->where('ptr_transacciones.role_id', $campos['role_id']);
            $q->where('ptr_transacciones.estado_id', 1);
            $q->whereHas('estado_politica', function($estado_politica) use ($pedido, $campos){
                $estado_politica->where('ptr_transacciones_catalogo.estado1', $pedido->id_estado);
                $estado_politica->where('ptr_transacciones_catalogo.estado2', $campos['estado_id']);
            });
        })
        ->join('ptr_transacciones_categoria', function($join){
            $join->on('ptr_transacciones_categoria.transaccion_id', "=", "ptr_transacciones.id");
        })
        ->where(function($aux) use ($pedido, $campos){           
         //$aux->whereIn('ptr_transacciones_categoria.categoria_id', $campos['categorias'] ); 
         foreach($pedido->pedidos_items as $pedidos_items){
                 $aux->where(function($aux) use ($pedidos_items, $campos){
                     $aux->whereIn('ptr_transacciones_categoria.categoria_id', $campos['categorias'] );
                 });
             }           
        })
        ->groupBy('ptr_transacciones.id')
        ->havingRaw('count(distinct ptr_transacciones_categoria.categoria_id) = '.count(array_unique($campos['categorias']->toArray())))
        ->get();
    }

    public function getCollectionPedidos($pedidos){

        return $pedidos->map(function($pedido){
            return $this->getPedido($pedido);
        });

    }

    public function getPedido($pedido){
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
            'uuid_lugar' => $pedido->uuid_lugar,
            'proveedor_id' => '',
            'referencia' => $pedido->referencia,
            'terminos_pago' => '',
            'articulos' => $articulo->get($pedido->pedidos_items, $pedido)
        ];

    }

    public function getCollectionPedidosAjax($pedidos){

        return $pedidos->map(function($pedido){
            $estado = count($pedido->estado) ? ' - '.$pedido->estado->etiqueta : '';
            $centro = count($pedido->centro_contable) ? ' - '.$pedido->centro_contable->nombre : '';
            return [
                //se usan para el catalogo de pedidos
                'id' => $pedido->id,
                'nombre' => $pedido->numero.$estado.$centro,
                'ajax' => true
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
