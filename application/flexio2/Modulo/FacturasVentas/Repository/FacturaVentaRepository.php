<?php

namespace Flexio\Modulo\FacturasVentas\Repository;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\FacturasVentas\Models\FacturaVenta as FacturaVenta;
use Flexio\Modulo\FacturasVentas\Models\FacturaVentaCatalogo as FacturaVentaCatalogo;
use Flexio\Modulo\Cotizaciones\Models\LineItemTransformer as LineItemTransformer;
use Flexio\Modulo\Inventarios\Models\Unidades as Unidades;
use Flexio\Modulo\CentroFacturable\Models\CentroFacturable;
use Flexio\Modulo\OrdenesVentas\Repository\OrdenVentaRepository;
use Flexio\Modulo\Comentario\Models\Comentario;

//servicios
use Flexio\Modulo\Base\Services\Numero as Numero;

class FacturaVentaRepository implements FacturaVentaInterface {

 	protected $OrdenVentaRepository;

	public function __construct() {
 		$this->OrdenVentaRepository  = new OrdenVentaRepository();
	}

    function find($id) {
        return FacturaVenta::find($id);
    }

    function getAll($clause) {
        return FacturaVenta::where(function($query) use($clause) {
                    $query->where('empresa_id', '=', $clause['empresa_id']);
                    if (!empty($clause['formulario']))
                        $query->whereIn('formulario', $clause['formulario']);
                    if (!empty($clause['estado']))
                        $query->whereIn('estado', $clause['estado']);
                })->get();
    }
    
    public function getLastEstadoHistory($id) {
        return Capsule::table('revisions as i')
                ->select(capsule::raw('CONCAT(usr.nombre, " " , usr.apellido) as usuario, i.*'))
                ->join('usuarios as usr', 'i.user_id','=', 'usr.id')
                ->where('revisionable_id', '=', $id)
                ->where('key', 'estado')
                ->orderBy('i.created_at', 'desc')
                ->first();
    }

    public function getCollectionCellDeItem($factura, $item_id) {

        $item = $factura->items2->filter(function($item) use ($item_id) {
                    return $item->id == $item_id;
                })->values(); //reset index

        $precio_unidad = new Numero('moneda', $item[0]->pivot->precio_unidad);
        $total = new Numero('moneda', $item[0]->pivot->precio_total);

        $unidad = Unidades::find($item[0]->pivot->unidad_id);

        return [
            $factura->fecha_desde,
            $factura->numero_documento_enlace,
            '<a class="link">' . $factura->cliente->nombre . ' ' . $factura->cliente->apellido . '</a>',
            //$factura->etapa_catalogo->valor,
            $factura->present()->estado_label,
            $item[0]->pivot->cantidad . ' ' . $unidad->nombre,
            $precio_unidad->getSalida(),
            $total->getSalida()
        ];
    }

    function getLastCodigo($clause=[]){
    	$count = FacturaVenta::where($clause)->count();
    	return sprintf('%06d', $count + 1);
    }

    function paraCrearCobro($clause) {
        return FacturaVenta::porCobrar()->where(function($query) use($clause) {
                    $query->where('empresa_id', '=', $clause['empresa_id']);
                })->get();
    }

    function paraVerCobro($clause) {
        return FacturaVenta::cobradoParcialCompleto()->where(function($query) use($clause) {
                    $query->where('empresa_id', '=', $clause['empresa_id']);
                })->get();
    }

    function cobradoCompletoSinNotaCredito($clause) {
        return FacturaVenta::cobradoCompleto()->has('nota_credito', '<', 1)->where(function($query) use($clause) {
                    $query->where('empresa_id', '=', $clause['empresa_id']);
                })->get();
    }
    function estadoValidosSinNotaCredito($clause) {
        return FacturaVenta::estadosValidos()->has('nota_credito', '<', 1)->where(function($query) use($clause) {
                    $query->where('empresa_id', '=', $clause['empresa_id']);
                })->get();
    }

    function sinDevolucion($clause) {
        return FacturaVenta::has('devolucion', '=', 0)->where(function($query) use($clause) {
                    $query->where('empresa_id', '=', $clause['empresa_id']);
                    if (!empty($clause['formulario']))
                        $query->whereIn('formulario', $clause['formulario']);
                    if (!empty($clause['estado']))
                        $query->whereIn('estado', $clause['estado']);
                })->get();
    }

    function create($created) {
        //evito obtener lineitem_id desde una orden de venta, etc.
        $created['lineitem'] = array_map(function($line_item){
          return array_merge($line_item, ["lineitem_id" => ""]);
        }, $created['lineitem']);

        $factura_venta = FacturaVenta::create($created['facturaventa']);
        $lineItem = new LineItemTransformer;
        $items = $lineItem->crearInstancia($created['lineitem']);

        $factura_venta->items()->saveMany($items);
         //aqui se agreg� la condicion para que almacene los comentarios que vienen de la cotizacion
        $comentarios_duplicados = $this->buscar_comentarios($created);
        $factura_venta->comentario_timeline()->saveMany($comentarios_duplicados);

        $this->addPolymorphicRelationship($factura_venta->centro_facturacion_id,$factura_venta);
        return $factura_venta;
    }

    function crear($crear) {
        if (empty($crear['factura_id'])) {
            $factura = FacturaVenta::create($crear);
        } else {
            $factura = FacturaVenta::find($crear['factura_id']);
            $factura->update($crear);
        }
        return $factura;
    }

    function update($update) {
        $factura_venta = FacturaVenta::find($update['facturaventa']['factura_id']);
        $factura_venta->update($update['facturaventa']);
        $lineItem = new LineItemTransformer;
        
        $items = $lineItem->crearInstancia($update['lineitem']);
        $factura_venta->items()->saveMany($items);
        $this->addPolymorphicRelationship($factura_venta->centro_facturacion_id,$factura_venta);
        return $factura_venta;
    }

    function buscar_comentarios($created) {
    	$comentariosNuevos = [];
    	$orden_venta=  $this->OrdenVentaRepository->findByUuid($created['venta_uuid']);

    	$comentarios = Comentario::where('comentable_id', '=', $orden_venta->id)->where('comentable_type', '=', 'Flexio\Modulo\OrdenesVentas\Models\OrdenVenta')->get();
     	$comentariosNuevos = $comentarios->each(function ($item, $key ) {
    		$copia_comentario = $item->replicate();
    		$copia_comentario->save();
    		$copia_comentario->comentable_type = 'Flexio\Modulo\OrdenesVentas\Models\FacturaVenta';
    		unset($copia_comentario->id);
    		unset($copia_comentario->comentable_id);
    		$comentarios_nuevos[] = $copia_comentario;
    		return $comentarios_nuevos;
    	});
    	return  $comentariosNuevos;
    }

    function findByUuid($uuid) {
        return FacturaVenta::where('uuid_factura', hex2bin($uuid))->first();
    }

    private function _filtros($query, $clause) {
        if (isset($clause["item_id"]) and ! empty($clause["item_id"])){$query->deItem($clause["item_id"]);}
        if (isset($clause['contrato_alquiler_id']) and !empty($clause['contrato_alquiler_id'])){$query->deContratoAlquiler($clause['contrato_alquiler_id']);}
        if (isset($clause['contrato_id']) and !empty($clause['contrato_id'])){$query->deContrato($clause['contrato_id']);}
    }

    function lista_totales($clause = array()) {
        return FacturaVenta::where(function($query) use($clause) {
                    $query->where('empresa_id', '=', $clause['empresa_id']);
                    $this->_filtros($query, $clause);
                    if (isset($clause['cliente_id']))
                        $query->where('cliente_id', '=', $clause['cliente_id']);
                    if (isset($clause['formulario']))
                        $query->where('formulario', '=', $clause['formulario']);
                    if (isset($clause['estado']))
                        $query->where('estado', '=', $clause['estado']);
                    if (isset($clause['creado_por']))
                        $query->where('created_by', '=', $clause['creado_por']);
                    if (isset($clause['fecha_desde']))
                        $query->where('fecha_desde', '<=', $clause['fecha_desde']);
                    if (isset($clause['fecha_hasta']))
                        $query->where('fecha_hasta', '>=', $clause['fecha_hasta']);



                })->count();
    }

    /**
     * @function de listar y busqueda
     */
    public function listar($clause = array(), $sidx = NULL, $sord = NULL, $limit = NULL, $start = NULL) {

        $facturas = FacturaVenta::where(function($query) use($clause) {
                   // $query->where('empresa_id','=',$clause['empresa_id']);
                    $this->_filtros($query, $clause);
                    //if(isset($clause['codigo']))$query->where($clause["codigo"]);
                    if (isset($clause['cliente_id']))
                        $query->where('cliente_id', $clause['cliente_id']);
                   // if (isset($clause['estado']))
                       // $query->where('estado1', '=', $clause['estado']);
                    //if (isset($clause['created_by']))
                       // $query->where('created_by', '=', $clause['created_by']);
                    //if(isset($clause['fecha_desde']))$query->where('fecha_desde','>=',$clause['fecha_desde']);
                    //if(isset($clause['fecha_hasta']))$query->where('fecha_desde','<=',$clause['fecha_desde']);
                });

        if(isset($clause['contrato_alquiler_id'])){unset($clause['contrato_alquiler_id']);}
        if(isset($clause['contrato_id'])){unset($clause['contrato_id']);}
        if(isset($clause['orden_alquiler_id'])){unset($clause['orden_alquiler_id']);}
        if(isset($clause['item_id'])){unset($clause['item_id']);}
        if(isset($clause['_search'])){unset($clause['_search']);}
        if(isset($clause['nd'])){unset($clause['nd']);}
        if(isset($clause['rows'])){unset($clause['rows']);}
        if(isset($clause['page'])){unset($clause['page']);}
        if(isset($clause['sidx'])){unset($clause['sidx']);}
        if(isset($clause['sord'])){unset($clause['sord']);}
        foreach ($clause AS $field => $value) {

            //Verificar si el campo tiene el simbolo @ y removerselo.
            if (preg_match('/@/i', $field)) {
                $field = str_replace("@", "", $field);
            }

            //verificar si valor es array
            //verificar si valor es array
            if (is_array($value)) {
                $facturas->where($field, $value[0], $value[1]);
            } else {
                $facturas->where($field, '=', $value);
            }
        }

        if ($sidx != NULL && $sord != NULL)
            $facturas->orderBy($sidx, $sord);
        if ($limit != NULL)
            $facturas->skip($start)->take($limit);
        return $facturas->get();
    }

    public static function exportar($clause = array()) {

        $query = FacturaVenta::where(function($query) use($clause) {
                    if (!empty($sidx) && preg_match("/cargo/i", $sidx)) {
                        $query->orderBy("nombre", $sord);
                    }
                });


        //Si existen variables de limite
        if ($clause != NULL && !empty($clause) && is_array($clause)) {
            foreach ($clause AS $field => $value) {
                $i = 0;
                foreach ($value AS $row) {

                    $valor_fin[$i] = hex2bin($row);

                    $i++;
                }
                //verificar si valor es array
                if (is_array($value)) {


                    $query->whereIn("uuid_factura", $valor_fin);

                } else {
                    $query->where($field, '=', $valor_fin);
                }
            }
        }

        return $query->get();
    }

    function addPolymorphicRelationship($centro_facturable_id,$factura) {

        if($centro_facturable_id!=0)
        {
            $centro_facturacion = CentroFacturable::find($centro_facturable_id);
            $centro_facturacion->factura()->sync([$factura->id]);
        }
    }
    function agregarComentario($ordenId, $comentarios) {
    	$factura = FacturaVenta::find($ordenId);
    	$comentario = new Comentario($comentarios);
    	$factura->comentario_timeline()->save($comentario);
    	return $factura;
    }
}
