<?php
namespace Flexio\Modulo\OrdenesAlquiler\Repository;
use Flexio\Modulo\OrdenesAlquiler\Models\OrdenVentaAlquiler;
use Flexio\Modulo\OrdenesAlquiler\Models\OrdenVentaAlquilerCatalogo as OrdenVentaCatalogo;
use Flexio\Modulo\Cotizaciones\Models\LineItemTransformer as LineItemTransformer;
use Flexio\Modulo\Contabilidad\Repository\CuentasRepository;
use Flexio\Modulo\Contabilidad\Repository\ImpuestosRepository;
use Flexio\Modulo\CentroFacturable\Models\CentroFacturable;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\Cotizaciones\Repository\CotizacionRepository;

class OrdenVentaAlquilerRepository{

    protected $CuentasRepository;
    protected $ImpuestosRepository;
    protected $CotizacionRepository;

    public function __construct() {
        $this->CuentasRepository    = new CuentasRepository();
        $this->ImpuestosRepository  = new ImpuestosRepository();
        $this->CotizacionRepository  = new CotizacionRepository();
    }

  function find($id) {
    return OrdenVentaAlquiler::find($id);
  }

  function getAll($clause) {
    return OrdenVentaAlquiler::where('empresa_id', '=', $clause['empresa_id'])->get();
  }

    public function getCollectionOrdenVenta($orden_alquiler){

        $articulo = new \Flexio\Library\Articulos\ArticuloVenta;
        return collect(array_merge(
                [
                    'centros_facturacion' => count($orden_alquiler->cliente->centro_facturable) ? $orden_alquiler->cliente->centro_facturable : [],
                ],
                $orden_alquiler->toArray(),
                [
                    'articulos' => $articulo->get($orden_alquiler->items_adicionales),
                    'articulos_alquiler' => $articulo->get($orden_alquiler->items_alquiler, $orden_alquiler),
                    'observaciones' => $orden_alquiler->comentario,
                    'comentario_timeline' => $orden_alquiler->comentario_timeline,
                    'saldo_cliente' => 0,
                    'credito_cliente' => 0,
                    'nombre' => "{$orden_alquiler->codigo} - {$orden_alquiler->cliente->nombre}",
                    'creado_por' => $orden_alquiler->created_by

                ]
        ));

    }

    public function _sync_items($orden_alquiler, $items){

        $orden_alquiler->items()->whereNotIn('id',array_pluck($items,'id_pedido_item'))->delete();
        foreach ($items as $item) {
            $orden_alquiler_item_id = (isset($item['id_pedido_item']) and !empty($item['id_pedido_item'])) ? $item['id_pedido_item'] : '';
            $orden_alquiler_item                          = $orden_alquiler->items()->firstOrNew(['id'=>$orden_alquiler_item_id]);
            $orden_alquiler_item->categoria_id            = $item['categoria'];
            $orden_alquiler_item->item_id                 = !empty($item['item_id']) ? $item['item_id'] : "";
            $orden_alquiler_item->item_adicional          = !empty($item['item_adicional']) ? $item['item_adicional'] : "";
            $orden_alquiler_item->empresa_id              = !empty($item['empresa_id']) ? $item['empresa_id'] : "";
            $orden_alquiler_item->cantidad                = !empty($item['cantidad']) ? $item['cantidad'] : "";
            $orden_alquiler_item->unidad_id               = !empty($item['unidad']) ? $item['unidad'] : "";
            $orden_alquiler_item->precio_unidad           = !empty($item['precio_unidad']) ? str_replace(",", "", $item['precio_unidad']) : "";
            //empezando desde un contrato de alquiler impuesto_id viene en un campo llamado "impuesto"
            //verificar si en otro escenario se llama "impuesto_id"
            $orden_alquiler_item->impuesto_id             = !empty($item['impuesto']) ? $item['impuesto'] : "";
            $orden_alquiler_item->descuento               = !empty($item['descuento']) ? $item['descuento'] : "";
            $orden_alquiler_item->cuenta_id               = !empty($item['cuenta']) ? $item['cuenta'] : "";
            $orden_alquiler_item->precio_total            = !empty($item['precio_total']) ? $item['precio_total'] : "";
            $orden_alquiler_item->atributo_id             = isset($item['atributo_id']) ? $item['atributo_id'] : '';
            $orden_alquiler_item->atributo_text           = isset($item['atributo_text']) ? $item['atributo_text'] : '';
            $orden_alquiler_item->impuesto_total          = !empty($item['impuesto_total']) ? $item['impuesto_total'] : "";
            $orden_alquiler_item->descuento_total         = !empty($item['descuento_total']) ? $item['descuento_total'] : "";
            $orden_alquiler_item->comentario              = (isset($item['comentario']))?$item['comentario']:'';
            $orden_alquiler_item->tarifa_periodo_id       = !empty($item['tarifa_periodo_id']) ? $item['tarifa_periodo_id'] : "";
            $orden_alquiler_item->tarifa_fecha_desde      = !empty($item['tarifa_fecha_desde']) ? $item['tarifa_fecha_desde'] : "";
            $orden_alquiler_item->tarifa_fecha_hasta      = !empty($item['tarifa_fecha_hasta']) ? $item['tarifa_fecha_hasta'] : "";
            $orden_alquiler_item->tarifa_pactada          = !empty($item['tarifa_pactada']) ? $item['tarifa_pactada'] : "";
            $orden_alquiler_item->tarifa_monto            = !empty($item['tarifa_monto']) ? $item['tarifa_monto'] : "";
            $orden_alquiler_item->tarifa_cantidad_periodo = !empty($item['tarifa_cantidad_periodo']) ? $item['tarifa_cantidad_periodo'] : "";
            $orden_alquiler_item->save();
        }
    }

    public function create($created) {
        //dd($created);
        $orden_alquiler = OrdenVentaAlquiler::create($created['ordenalquiler']);
        $this->_sync_items($orden_alquiler, $created['lineitem']);
        $comentarios_duplicados = $this->buscar_comentarios($created);
        $orden_alquiler->comentario_timeline()->saveMany($comentarios_duplicados);

        $this->addPolymorphicRelationship($orden_alquiler->centro_facturacion_id,$orden_alquiler);

        return $orden_alquiler;
    }

    public function update($update) {
        $orden_alquiler = OrdenVentaAlquiler::find($update['ordenalquiler']['id']);
        $orden_alquiler->update($update['ordenalquiler']);

        $this->_sync_items($orden_alquiler, $update['lineitem']);
        $this->addPolymorphicRelationship($orden_alquiler->centro_facturacion_id,$orden_alquiler);
        return $orden_alquiler;
    }

    function buscar_comentarios($created) {
    	$comentariosNuevos = [];
      $cotizacion = isset($created['cotizacion_uuid']) ? $this->CotizacionRepository ->findByUuid($created['cotizacion_uuid']) : $this->CotizacionRepository->find($created['ordenalquiler']['contrato_id']);//$cotizacion_id->id
     	$comentarios = Comentario::where('comentable_id', '=', (!empty($cotizacion) ? $cotizacion->id : ""))->where('comentable_type', '=', 'Flexio\Modulo\OrdenesAlquiler\Models\OrdenVenta')->get();

     	$comentariosNuevos = $comentarios->each(function ($item, $key ) {
      		$copia_comentario = $item->replicate();
      		$copia_comentario->save();
      		$copia_comentario->comentable_type = 'Flexio\Modulo\OrdenesAlquiler\Models\OrdenVenta';
      		unset($copia_comentario->id);
      		unset($copia_comentario->comentable_id);
     		$comentarios_nuevos[] = $copia_comentario;
      		return $comentarios_nuevos;
     	});
     	return  $comentariosNuevos;
     }

    function agregarComentario($ordenId, $comentarios) {
    	$ordenVenta = OrdenVentaAlquiler::find($ordenId);
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
    return OrdenVentaAlquiler::deEmpresa($clause)->estadoValido()->get();
  }
  function ordenesVentasValidasVer($clause) {
    return OrdenVentaAlquiler::deEmpresa($clause)->facturadoCompleto()->get();
  }

  function findByUuid($uuid) {
    return OrdenVentaAlquiler::where('uuid_venta',hex2bin($uuid))->with(array("contrato_alquiler", "contrato_alquiler.cliente"))->first();
  }

function lista_totales($clause=array()) {
  $query = OrdenVentaAlquiler::where(function($query) use($clause){
    $query->where('empresa_id','=',$clause['empresa_id']);
    //$query->where('formulario', '=', $clause['formulario']);
    if(isset($clause['cotizacion_id']))$query->where('cotizacion_id','=' ,$clause['cotizacion_id']);
    if(isset($clause['id']))$query->where('id','=' ,$clause['id']);
    if(isset($clause['cliente_id']))$query->where('cliente_id','=' ,$clause['cliente_id']);
    if(isset($clause['etapa']))$query->where('estado','=' ,$clause['etapa']);
    if(isset($clause['creado_por']))$query->where('created_by','=',$clause['creado_por']);
    if(isset($clause['fecha_desde']))$query->where('fecha_desde','<=',$clause['fecha_desde']);
    if(isset($clause['fecha_hasta']))$query->where('fecha_hasta','>=',$clause['fecha_hasta']);
    if(isset($clause['codigo']))$query->where('codigo','LIKE', "%". $clause['codigo'] ."%");
    if(isset($clause['campo']) and !empty($clause['campo']))$query->deFiltro($clause['campo']);
    })->with(array("contrato_alquiler"));
    
  $contrato_codigo = !empty($clause["no_contrato"]) ? $clause["no_contrato"] : array();

  //Clause Contrato
  if(!empty($contrato_codigo)){

    $query_contrato = \Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquiler::deEmpresa($clause);
    if(!empty($contrato_codigo)){
      $query_contrato->deCodigo($contrato_codigo);
    }
    $contratos = $query_contrato->get(array("id"))->pluck('id')->toArray();

    $query->whereIn("contrato_id", $contratos);
  }
  
  return $query->count();
}

/**
* @function de listar y busqueda
*/
public function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {
    
    $query = OrdenVentaAlquiler::where(function($query) use($clause){
        $query->where('empresa_id','=',$clause['empresa_id']);
        //$query->where('formulario', '=', $clause['formulario']);
        if(isset($clause['cotizacion_id']))$query->where('cotizacion_id','=' ,$clause['cotizacion_id']);
        if(isset($clause['id']))$query->whereIn('id', $clause['id']);
        if(isset($clause['cliente_id']))$query->where('cliente_id','=' ,$clause['cliente_id']);
        if(isset($clause['etapa']))$query->where('estado','=' ,$clause['etapa']);
        if(isset($clause['creado_por']))$query->where('created_by','=',$clause['creado_por']);
        if(isset($clause['fecha_desde']))$query->where('fecha_desde','<=',$clause['fecha_desde']);
        if(isset($clause['fecha_hasta']))$query->where('fecha_hasta','>=',$clause['fecha_hasta']);
        if(isset($clause['codigo']))$query->where('codigo','LIKE', "%". $clause['codigo'] ."%");
        if(isset($clause['campo']) and !empty($clause['campo']))$query->deFiltro($clause['campo']);
    }) //->with(array("contrato_alquiler"))
    ;
    $contrato_codigo = !empty($clause["no_contrato"]) ? $clause["no_contrato"] : array();

    //Clause Contrato
    if(!empty($contrato_codigo)){

      $query_contrato = \Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquiler::deEmpresa($clause);
      if(!empty($contrato_codigo)){
        $query_contrato->deCodigo($contrato_codigo);
      }
      $contratos = $query_contrato->get(array("id"))->pluck('id')->toArray();

      $query->whereIn("contrato_id", $contratos);
    }
    
    if($sidx!=NULL && $sord!=NULL) $query->orderBy($sidx, $sord);
    if($limit!=NULL) $query->skip($start)->take($limit);
    
    return $query->get();
}

    public function addPolymorphicRelationship($centro_facturable_id,$orden_alquiler) {
        if(empty($centro_facturable_id)){
            return;
        }
        $centro_facturacion = CentroFacturable::find($centro_facturable_id);
        $centro_facturacion->orden_alquiler()->sync([$orden_alquiler->id]);
    }
}
