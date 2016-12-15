<?php
namespace Flexio\Modulo\OrdenesVentas\Repository;
use Flexio\Modulo\OrdenesVentas\Models\OrdenVenta as OrdenVenta;
use Flexio\Modulo\OrdenesVentas\Models\OrdenVentaCatalogo as OrdenVentaCatalogo;
use Flexio\Modulo\Cotizaciones\Models\LineItemTransformer as LineItemTransformer;
use Flexio\Modulo\Contabilidad\Repository\CuentasRepository;
use Flexio\Modulo\Contabilidad\Repository\ImpuestosRepository;
use Flexio\Modulo\CentroFacturable\Models\CentroFacturable;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\Cotizaciones\Repository\CotizacionRepository;

class OrdenVentaRepository{

    protected $CuentasRepository;
    protected $ImpuestosRepository;
    protected $CotizacionRepository;

    public function __construct() {
        $this->CuentasRepository    = new CuentasRepository();
        $this->ImpuestosRepository  = new ImpuestosRepository();
        $this->CotizacionRepository  = new CotizacionRepository();
    }

  function find($id) {
    return OrdenVenta::find($id);
  }

  function getAll($clause) {
    return OrdenVenta::where('empresa_id', '=', $clause['empresa_id'])->get();
  }

    public function getCollectionOrdenVenta($orden_venta){

        $articulo = new \Flexio\Library\Articulos\ArticuloVenta;
        return collect(array_merge(
                [
                    'centros_facturacion' => count($orden_venta->cliente->centro_facturable) ? $orden_venta->cliente->centro_facturable : [],
                ],
                $orden_venta->toArray(),
                [
                    'articulos' => $articulo->get($orden_venta->items, $orden_venta),
                    'observaciones' => $orden_venta->comentario,
                    'comentario_timeline' => $orden_venta->comentario_timeline,
                    'saldo_cliente' => 0,
                    'credito_cliente' => 0,
                    'nombre' => "{$orden_venta->codigo} - {$orden_venta->cliente->nombre}",
                    'creado_por' => $orden_venta->created_by

                ]
        ));

    }

    public function _sync_items($orden_venta, $items){

        $orden_venta->items()->whereNotIn('id',array_pluck($items,'id_pedido_item'))->delete();
        foreach ($items as $item) {

            $orden_venta_item_id = (isset($item['id_pedido_item']) and !empty($item['id_pedido_item'])) ? $item['id_pedido_item'] : '';
            $orden_venta_item = $orden_venta->items()->firstOrNew(['id'=>$orden_venta_item_id]);
            $orden_venta_item->categoria_id = $item['categoria'];
            $orden_venta_item->item_id = $item['item_id'];
            $orden_venta_item->cantidad = $item['cantidad'];
            $orden_venta_item->unidad_id = $item['unidad'];
            $orden_venta_item->precio_unidad = $item['precio_unidad'];
            $orden_venta_item->precio_unidad = !empty($item['precio_unidad']) ? str_replace(",", "", $item['precio_unidad']) : "";
            $orden_venta_item->impuesto_id = $item['impuesto'];
            $orden_venta_item->descuento = $item['descuento'];
            $orden_venta_item->cuenta_id = $item['cuenta'];
            $orden_venta_item->precio_total = $item['precio_total'];
            $orden_venta_item->atributo_id = isset($item['atributo_id']) ? $item['atributo_id'] : '';
            $orden_venta_item->atributo_text = isset($item['atributo_text']) ? $item['atributo_text'] : '';
            $orden_venta_item->impuesto_total = $item['impuesto_total'];
            $orden_venta_item->descuento_total = $item['descuento_total'];
            $orden_venta_item->comentario = (isset($item['comentario']))?$item['comentario']:'';
            $orden_venta_item->save();

        }

    }

    public function create($created) {

        $orden_venta = OrdenVenta::create($created['ordenventa']);
        $this->_sync_items($orden_venta, $created['lineitem']);
        $comentarios_duplicados = $this->buscar_comentarios($created);

        $orden_venta->comentario_timeline()->saveMany($comentarios_duplicados);

        $this->addPolymorphicRelationship($orden_venta->centro_facturacion_id,$orden_venta);

        return $orden_venta;
    }

    public function update($update) {
        $orden_venta = OrdenVenta::find($update['ordenventa']['id']);
        $orden_venta->update($update['ordenventa']);
        $this->_sync_items($orden_venta, $update['lineitem']);
        $this->addPolymorphicRelationship($orden_venta->centro_facturacion_id,$orden_venta);
        return $orden_venta;
    }

    function buscar_comentarios($created) {
    	$comentariosNuevos = [];
        $cotizacion = isset($created['cotizacion_uuid']) ? $this->CotizacionRepository ->findByUuid($created['cotizacion_uuid']) : $this->CotizacionRepository->find($created['ordenventa']['cotizacion_id']);//$cotizacion_id->id
     	$comentarios = Comentario::where('comentable_id', '=', $cotizacion->id)->where('comentable_type', '=', 'Flexio\Modulo\Cotizaciones\Models\Cotizacion')->get();

     	$comentariosNuevos = $comentarios->each(function ($item, $key ) {
      		$copia_comentario = $item->replicate();
      		$copia_comentario->save();
      		$copia_comentario->comentable_type = 'Flexio\Modulo\OrdenesVentas\Models\OrdenVenta';
      		unset($copia_comentario->id);
      		unset($copia_comentario->comentable_id);
     		$comentarios_nuevos[] = $copia_comentario;
      		return $comentarios_nuevos;
     	});
     	return  $comentariosNuevos;
     }

    function agregarComentario($ordenId, $comentarios) {
    	$ordenVenta = OrdenVenta::find($ordenId);
    	$comentario = new Comentario($comentarios);
    	$ordenVenta->comentario_timeline()->save($comentario);
    	return $ordenVenta;
    }

    public function _getItems($items, $empresa_id) {
        $aux = [];

        foreach ($items as $item)
        {
            $impuesto   = is_numeric($item['impuesto_id'])? $this->ImpuestosRepository->find($item['impuesto_id']):$this->ImpuestosRepository->findByUuid($item['impuesto_id']);
            $cuenta     = is_numeric($item['cuenta_id'])?$this->CuentasRepository->find($item['cuenta_id']):$this->CuentasRepository->findByUuid($item['cuenta_id']);

            $total_impuesto         = ($impuesto->impuesto / 100) * ($item['cantidad'] * $item['precio_unidad']);
            $total_descuento        = ($item['descuento'] / 100) * ($item['cantidad'] * $item['precio_unidad']);

            $aux[$item['item_id']]['categoria_id'] = $item['categoria_id'];
            $aux[$item['item_id']]['cantidad'] = $item['cantidad'];
            $aux[$item['item_id']]['unidad_id'] = $item['unidad_id'];
            $aux[$item['item_id']]['precio_unidad'] = $item['precio_unidad'];
            $aux[$item['item_id']]['impuesto_id'] = $impuesto->id;
            $aux[$item['item_id']]['descuento'] = $item['descuento'];
            $aux[$item['item_id']]['cuenta_id'] = $cuenta->id;
            $aux[$item['item_id']]['precio_total'] = $item['precio_total'];
            $aux[$item['item_id']]['atributo_id'] = $item['atributo_id'];
            $aux[$item['item_id']]['empresa_id'] = $empresa_id;
            $aux[$item['item_id']]['impuesto_total'] = $total_impuesto;
            $aux[$item['item_id']]['descuento_total'] = $total_descuento;
            $aux[$item['item_id']]['comentario'] = (isset($item['comentario']))?$item['comentario']:'';

        }
        return $aux;
    }


  function ordenesVentasValidas($clause) {
    return OrdenVenta::deEmpresa($clause)->estadoValido()->get();
  }
  function ordenesVentasValidasVer($clause) {
    return OrdenVenta::deEmpresa($clause)->facturadoCompleto()->get();
  }

  function findByUuid($uuid) {
    return OrdenVenta::where('uuid_venta',hex2bin($uuid))->first();
  }

function lista_totales($clause=array()) {
  return OrdenVenta::where(function($query) use($clause){
    $query->where('empresa_id','=',$clause['empresa_id']);
    $query->where('formulario', '=', $clause['formulario']);
    if(isset($clause['cotizacion_id']))$query->where('cotizacion_id','=' ,$clause['cotizacion_id']);
    if(isset($clause['id']))$query->where('id','=' ,$clause['id']);
    if(isset($clause['cliente_id']))$query->where('cliente_id','=' ,$clause['cliente_id']);
    if(isset($clause['etapa']))$query->where('estado','=' ,$clause['etapa']);
    if(isset($clause['creado_por']))$query->where('created_by','=',$clause['creado_por']);
    if(isset($clause['fecha_desde']))$query->where('fecha_desde','<=',$clause['fecha_desde']);
    if(isset($clause['fecha_hasta']))$query->where('fecha_hasta','>=',$clause['fecha_hasta']);
  })->count();
}

/**
* @function de listar y busqueda
*/
public function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {
    $ordenes = OrdenVenta::where(function($query) use($clause){
        $query->where('empresa_id','=',$clause['empresa_id']);
        $query->where('formulario', '=', $clause['formulario']);
        if(isset($clause['cotizacion_id']))$query->where('cotizacion_id','=' ,$clause['cotizacion_id']);
        if(isset($clause['id']))$query->whereIn('id', $clause['id']);
        if(isset($clause['cliente_id']))$query->where('cliente_id','=' ,$clause['cliente_id']);
        if(isset($clause['etapa']))$query->where('estado','=' ,$clause['etapa']);
        if(isset($clause['creado_por']))$query->where('created_by','=',$clause['creado_por']);
        if(isset($clause['fecha_desde']))$query->where('fecha_desde','<=',$clause['fecha_desde']);
        if(isset($clause['fecha_hasta']))$query->where('fecha_hasta','>=',$clause['fecha_hasta']);
    });
    if($sidx!=NULL && $sord!=NULL) $ordenes->orderBy($sidx, $sord);
    if($limit!=NULL) $ordenes->skip($start)->take($limit);
  return $ordenes->get();
}

    public function addPolymorphicRelationship($centro_facturable_id,$orden_venta) {
        if(empty($centro_facturable_id)){
            return;
        }
        $centro_facturacion = CentroFacturable::find($centro_facturable_id);
        $centro_facturacion->orden_ventas()->sync([$orden_venta->id]);
    }
}
