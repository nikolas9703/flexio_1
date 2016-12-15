<?php

namespace Flexio\Modulo\Cotizaciones\Repository;

use Flexio\Modulo\Cotizaciones\Models\Cotizacion as Cotizacion;
use Flexio\Modulo\Cotizaciones\Models\CotizacionCatalogo as CotizacionCatalogo;
use Flexio\Modulo\Cotizaciones\Models\LineItemTransformer as LineItemTransformer;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\CentroFacturable\Models\CentroFacturable;

class CotizacionRepository {

    function find($id) {
        return Cotizacion::find($id);
    }

    function getAll($clause) {
        return Cotizacion::where('empresa_id', '=', $clause['empresa_id'])->get();
    }

    function getCotizacionValidas($clause) {
        return $cotizacion = Cotizacion::where(function($query) use($clause) {
                    $query->where('empresa_id', '=', $clause['empresa_id']);
                    $query->whereNotIn('estado', array('anulada', 'perdida'));
                })->get();
    }

    function getCotizacionAbierta($clause) {
        return $cotizacion = Cotizacion::where(function($query) use($clause) {
                    $query->where('empresa_id', '=', $clause['empresa_id']);
                    $query->whereIn('estado', array('aprobado'));
                })->get();
    }

    public function getCotizacionOrdenables($clause) {
        return $cotizacion = Cotizacion::where(function($query) use($clause) {
                    $query->where('empresa_id',$clause['empresa_id']);
                    $query->where('estado','aprobado');
                    $query->where('cliente_tipo','cliente');
                })->get();
    }

    public function getCollectionCotizacionesEmpezarDesde($cotizaciones){

        return $cotizaciones->map(function($cotizacion){

            return $this->getCollectionCotizacionEmpezarDesde($cotizacion);

        });

    }

    public function getCollectionCotizacionEmpezarDesde($cotizacion){

        $articulo = new \Flexio\Library\Articulos\ArticuloVenta;
        return collect(array_merge(
                [
                    'centros_facturacion' => count($cotizacion->cliente->centro_facturable) ? $cotizacion->cliente->centro_facturable : [],
                ],
                $cotizacion->toArray(),
                [
                    'articulos' => $articulo->get($cotizacion->items, $cotizacion),
                    'observaciones' => $cotizacion->comentario,
                    'saldo_cliente' => 0,
                    'credito_cliente' => 0,
                    'nombre' => "{$cotizacion->codigo} - {$cotizacion->cliente->nombre}",
                    'estado' => 'abierta'//Este estado pertenece al catalogo de ordenes de venta. No es un estado de la cotizacion
                ]
        ));

    }

    public function getCollectionCotizacion($cotizacion){

        $articulo = new \Flexio\Library\Articulos\ArticuloVenta;
        return collect(array_merge(
                [
                    'centros_facturacion' => count($cotizacion->cliente->centro_facturable) ? $cotizacion->cliente->centro_facturable : [],
                ],
                $cotizacion->toArray(),
                [
                    'articulos' => $articulo->get($cotizacion->items, $cotizacion),
                    'observaciones' => $cotizacion->comentario,
                    'comentario_timeline' => $cotizacion->comentario_timeline,
                    'saldo_cliente' => 0,
                    'credito_cliente' => 0,
                    'nombre' => "{$cotizacion->codigo} - {$cotizacion->cliente->nombre}"
                ]
        ));

    }

    function create($created) {

        $cotizacion = Cotizacion::create($created['cotizacion']);

        if(isset($created['cotizacion']['oportunidad_id']) and !empty($created['cotizacion']['oportunidad_id'])){

            $cotizacion->oportunidades()->attach($created['cotizacion']['oportunidad_id']);

        }

        $this->_sync_items($cotizacion, $created['lineitem']);
        $this->addPolymorphicRelationship($created['cotizacion']['centro_facturacion_id'],$cotizacion);
        return $cotizacion;
    }

    function update($update) {
        $cotizacion = Cotizacion::find($update['cotizacion']['id']);
        $cotizacion->update($update['cotizacion']);

        $this->_sync_items($cotizacion, $update['lineitem']);
        $this->addPolymorphicRelationship($update['cotizacion']['centro_facturacion_id'],$cotizacion);
        return $cotizacion;
    }

    public function _sync_items($cotizacion, $items){

        $cotizacion->items()->whereNotIn('id',array_pluck($items,'id_pedido_item'))->delete();
        foreach ($items as $item) {

            $cotizacion_item_id = (isset($item['id_pedido_item']) and !empty($item['id_pedido_item'])) ? $item['id_pedido_item'] : '';
            $cotizacion_item = $cotizacion->items()->firstOrNew(['id'=>$cotizacion_item_id]);
            $cotizacion_item->categoria_id = $item['categoria'];
            $cotizacion_item->item_id = $item['item_id'];
            $cotizacion_item->cantidad = $item['cantidad'];
            $cotizacion_item->unidad_id = $item['unidad'];
            $cotizacion_item->precio_unidad = str_replace(',','',$item['precio_unidad']); ///$item['precio_unidad'];
            $cotizacion_item->impuesto_id = $item['impuesto'];
            $cotizacion_item->descuento = $item['descuento'];
            $cotizacion_item->cuenta_id = $item['cuenta'];
            $cotizacion_item->precio_total = str_replace(',','',$item['precio_total']);
            $cotizacion_item->atributo_id = isset($item['atributo_id']) ? $item['atributo_id'] : 0;
            $cotizacion_item->atributo_text = isset($item['atributo_text']) ? $item['atributo_text'] : '';
            $cotizacion_item->impuesto_total = str_replace(',','',$item['impuesto_total']);
            $cotizacion_item->descuento_total = str_replace(',','',$item['descuento_total']);
            $cotizacion_item->comentario = (isset($item['comentario']))?$item['comentario']:'';
            $cotizacion_item->save();

        }
    }



  function agregarComentario($ordenId, $comentarios) {
  	$cotizacion = Cotizacion::find($ordenId);
 	  $comentario = new Comentario($comentarios);
    $cotizacion->comentario_timeline()->save($comentario);
  	return $cotizacion;
  }

    function findByUuid($uuid) {
        return Cotizacion::where('uuid_cotizacion', hex2bin($uuid))->first();
    }

    private function _filtros($query, $clause){

        if(isset($clause['oportunidad_id']) and !empty($clause['oportunidad_id'])){$query->deOportunidad($clause['oportunidad_id']);}
        if(isset($clause['orden_venta_id']) and !empty($clause['orden_venta_id'])){$query->deOrdenVenta($clause['orden_venta_id']);}

    }

    public function lista_totales($clause = array()) {
        return Cotizacion::where(function($query) use($clause) {
                    $this->_filtros($query, $clause);
                    $query->where('empresa_id', '=', $clause['empresa_id']);
                    if (isset($clause['cliente_id']))
                        $query->where('cliente_id', '=', $clause['cliente_id']);
                    if (isset($clause['id']))
                        $query->where('id', '=', $clause['id']);
                    if (isset($clause['etapa']))
                        $query->where('estado', '=', $clause['etapa']);
                    if (isset($clause['creado_por']))
                        $query->where('creado_por', '=', $clause['creado_por']);
                    if (isset($clause['fecha_desde']))
                        $query->where('fecha_desde', '<=', $clause['fecha_desde']);
                    if (isset($clause['fecha_hasta']))
                        $query->where('fecha_hasta', '>=', $clause['fecha_hasta']);
                    if(isset($clause['no_cotizacion']) and !empty($clause['no_cotizacion'])){$query->deNoCotizacion($clause['no_cotizacion']);}
                })->count();
    }

    function listar($clause = array(), $sidx = NULL, $sord = NULL, $limit = NULL, $start = NULL) {
        $cotizacion = Cotizacion::where(function($query) use($clause) {
                    $this->_filtros($query, $clause);
                    $query->where('empresa_id', '=', $clause['empresa_id']);
                    if (isset($clause['cliente_id']))
                        $query->where('cliente_id', '=', $clause['cliente_id']);
                    if (isset($clause['id']))
                        $query->where('id', '=', $clause['id']);
                    if (isset($clause['estado']))
                        $query->where('estado', '=', $clause['estado']);
                    if (isset($clause['creado_por']))
                        $query->where('creado_por', '=', $clause['creado_por']);
                    if (isset($clause['fecha_desde']))
                        $query->where('fecha_desde', '<=', $clause['fecha_desde']);
                    if (isset($clause['fecha_hasta']))
                        $query->where('fecha_hasta', '>=', $clause['fecha_hasta']);
                    if(isset($clause['no_cotizacion']) and !empty($clause['no_cotizacion'])){$query->deNoCotizacion($clause['no_cotizacion']);}
                });
        if ($sidx !== NULL && $sord !== NULL)
            $cotizacion->orderBy($sidx, $sord);
        if ($limit != NULL)
            $cotizacion->skip($start)->take($limit);
        return $cotizacion->get();
    }

    public function addPolymorphicRelationship($centro_facturable_id,$cotizacion) {
        if(empty($centro_facturable_id)){
            return;
        }
        $centro_facturacion = CentroFacturable::find($centro_facturable_id);
        $centro_facturacion->cotizacion()->sync([$cotizacion->id]);
    }

}
