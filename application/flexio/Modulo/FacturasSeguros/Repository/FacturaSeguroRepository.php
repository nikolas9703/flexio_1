<?php

namespace Flexio\Modulo\FacturasSeguros\Repository;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\FacturasSeguros\Models\FacturaSeguro as FacturaSeguro;
use Flexio\Modulo\FacturasSeguros\Models\FacturaSeguroCatalogo as FacturaSeguroCatalogo;
use Flexio\Modulo\Cotizaciones\Models\LineItemTransformer as LineItemTransformer;
use Flexio\Modulo\Inventarios\Models\Unidades as Unidades;
use Flexio\Modulo\CentroFacturable\Models\CentroFacturable;
use Flexio\Modulo\OrdenesVentas\Repository\OrdenVentaRepository;
use Flexio\Modulo\Comentario\Models\Comentario;

//servicios
use Flexio\Modulo\Base\Services\Numero as Numero;

class FacturaSeguroRepository implements FacturaSeguroInterface {

    protected $OrdenVentaRepository;

    public function __construct() {
        $this->OrdenVentaRepository  = new OrdenVentaRepository();
    }

    function find($id) {
        return FacturaSeguro::find($id);
    }

    function getAll($clause) {
        return FacturaSeguro::where(function($query) use($clause) {
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
        $count = FacturaSeguro::where($clause)->count();
        return sprintf('%06d', $count + 1);
    }

    function paraCrearCobro($clause) {
        return FacturaSeguro::porCobrar()->where(function($query) use($clause) {
                    $query->where('empresa_id', '=', $clause['empresa_id']);
                })->get();
    }

    function paraVerCobro($clause) {
        return FacturaSeguro::cobradoParcialCompleto()->where(function($query) use($clause) {
                    $query->where('empresa_id', '=', $clause['empresa_id']);
                })->get();
    }

    function cobradoCompletoSinNotaCredito($clause) {
        return FacturaSeguro::cobradoCompleto()->has('nota_credito', '<', 1)->where(function($query) use($clause) {
                    $query->where('empresa_id', '=', $clause['empresa_id']);
                })->get();
    }
    function estadoValidosSinNotaCredito($clause) {
        return FacturaSeguro::estadosValidos()->has('nota_credito', '<', 1)->where(function($query) use($clause) {
                    $query->where('empresa_id', '=', $clause['empresa_id']);
                })->get();
    }

    function sinDevolucion($clause) {
        return FacturaSeguro::has('devolucion', '=', 0)->where(function($query) use($clause) {
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
        $factura_venta = FacturaSeguro::create($created['facturaVenta']);
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
            $factura = FacturaSeguro::create($crear);
        } else {
            $factura = FacturaSeguro::find($crear['factura_id']);
            $factura->update($crear);
        }
        return $factura;
    }

    function update($update) {
        $factura_venta = FacturaSeguro::find($update['facturaVenta']['factura_id']);
        $factura_venta->update($update['facturaVenta']);
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
            $copia_comentario->comentable_type = 'Flexio\Modulo\OrdenesVentas\Models\FacturaSeguro';
            unset($copia_comentario->id);
            unset($copia_comentario->comentable_id);
            $comentarios_nuevos[] = $copia_comentario;
            return $comentarios_nuevos;
        });
        return  $comentariosNuevos;
    }

    function findByUuid($uuid) {
        return FacturaSeguro::where('uuid_factura', hex2bin($uuid))->first();
    }

    private function _filtros($query, $clause) {
        if (isset($clause["item_id"]) and ! empty($clause["item_id"])){$query->deItem($clause["item_id"]);}
        if (isset($clause['contrato_alquiler_id']) and !empty($clause['contrato_alquiler_id'])){$query->deContratoAlquiler($clause['contrato_alquiler_id']);}
        if (isset($clause['contrato_id']) and !empty($clause['contrato_id'])){$query->deContrato($clause['contrato_id']);}
        
    }

    function lista_totales($clause = array()) {
        $facturas = FacturaSeguro::join("cli_clientes", "cli_clientes.id", "=", "fac_facturas.cliente_id")
                        ->join("pol_polizas", "pol_polizas.id", "=", "fac_facturas.id_poliza")
                        ->join("cen_centros","cen_centros.id","=","fac_facturas.centro_contable_id")
                        ->join("pol_poliza_prima", "pol_poliza_prima.id_poliza", "=", "fac_facturas.id_poliza")
                        ->where(function($query) use($clause) {
                            $query->where('fac_facturas.empresa_id','=',$clause['empresa_id']);
                            //$query->where('fac_facturas.estado','!=',"por_aprobar");
                            $this->_filtros($query, $clause);
                            if (isset($clause['codigo'])) $query->where("fac_facturas.codigo", "LIKE", "%".$clause['codigo']."%");
                            if (isset($clause['numero'])) $query->where("pol_polizas.numero", "LIKE", "%".$clause['numero']."%");
                            if (isset($clause['cliente_id'])) $query->where("cli_clientes.id","=", $clause['cliente_id']);
                            if (isset($clause['ramo']) && count($clause['ramo'])>0) $query->whereIn("pol_polizas.ramo", $clause['ramo']);
                            if (isset($clause['sitiopago'])) $query->where("pol_poliza_prima.sitio_pago","=", $clause['sitiopago']);
                            if (isset($clause['estado'])){ $query->where('fac_facturas.estado', '=', $clause['estado']);}else{
                                $query->where('fac_facturas.estado', '<>', "por_aprobar");
                            }                            
                            if(isset($clause['fecha_desde']))$query->where('fac_facturas.fecha_desde','>=',$clause['fecha_desde']);
                            if(isset($clause['fecha_hasta']))$query->where('fac_facturas.fecha_desde','<=',$clause['fecha_hasta']);
                            if(isset($clause['fecha_vencimiento_desde']))$query->where('fac_facturas.fecha_hasta','>=',$clause['fecha_vencimiento_desde']);
                            if(isset($clause['fecha_vencimiento_hasta']))$query->where('fac_facturas.fecha_hasta','<=',$clause['fecha_vencimiento_hasta']);
                            $query->where("fac_facturas.formulario", "facturas_seguro");
                            
                        });

        $facturas->select("fac_facturas.id","fac_facturas.uuid_factura", "fac_facturas.codigo", "fac_facturas.fecha_desde", "fac_facturas.fecha_hasta", "fac_facturas.total", "cli_clientes.nombre", "fac_facturas.estado", "fac_facturas.centro_contable_id", "fac_facturas.cliente_id", "pol_polizas.numero", "pol_polizas.ramo", "pol_poliza_prima.sitio_pago");
        
        //print_r($facturas->toSql());
        return $facturas->count();
    }

    /**
     * @function de listar y busqueda
     */
    public function listar($clause = array(), $sidx = NULL, $sord = NULL, $limit = NULL, $start = NULL) {

        $facturas = FacturaSeguro::where(function($query) use($clause) {
                   // $query->where('empresa_id','=',$clause['empresa_id']);
                    $this->_filtros($query, $clause);
                    //if(isset($clause['codigo']))$query->where($clause["codigo"]);
                    if (isset($clause['cliente_id']))
                        $query->where('cliente_id', $clause['cliente_id']);
                    if (isset($clause['campo']) and !empty($clause['campo']))
                        $query->deFiltro($clause['campo']);
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
        if(isset($clause['campo']))unset($clause['campo']);
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


    public function listar_tabla($clause = array(), $sidx = NULL, $sord = NULL, $limit = NULL, $start = NULL) {

        $facturas = FacturaSeguro::join("cli_clientes", "cli_clientes.id", "=", "fac_facturas.cliente_id")
                        ->join("pol_polizas", "pol_polizas.id", "=", "fac_facturas.id_poliza")
                        ->join("cen_centros","cen_centros.id","=","fac_facturas.centro_contable_id")
                        ->join("pol_poliza_prima", "pol_poliza_prima.id_poliza", "=", "fac_facturas.id_poliza")
                        ->where(function($query) use($clause) {
                            $query->where('fac_facturas.empresa_id','=',$clause['empresa_id']);
                            //$query->where('fac_facturas.estado','!=',"por_aprobar");
                            $this->_filtros($query, $clause);
                            if (isset($clause['codigo'])) $query->where("fac_facturas.codigo", "LIKE", "%".$clause['codigo']."%");
                            if (isset($clause['numero'])) $query->where("pol_polizas.numero", "LIKE", "%".$clause['numero']."%");
                            if (isset($clause['cliente_id'])) $query->where("cli_clientes.id","=", $clause['cliente_id']);
                            if (isset($clause['ramo']) && count($clause['ramo'])>0) $query->whereIn("pol_polizas.ramo", $clause['ramo']);
                            if (isset($clause['sitiopago'])) $query->where("pol_poliza_prima.sitio_pago","=", $clause['sitiopago']);
                            if (isset($clause['estado'])){ $query->where('fac_facturas.estado', '=', $clause['estado']);}else{
                                $query->where('fac_facturas.estado', '<>', "por_aprobar");
                            }                            
                            if(isset($clause['fecha_desde']))$query->where('fac_facturas.fecha_desde','>=',$clause['fecha_desde']);
                            if(isset($clause['fecha_hasta']))$query->where('fac_facturas.fecha_desde','<=',$clause['fecha_hasta']);
                            if(isset($clause['fecha_vencimiento_desde']))$query->where('fac_facturas.fecha_hasta','>=',$clause['fecha_vencimiento_desde']);
                            if(isset($clause['fecha_vencimiento_hasta']))$query->where('fac_facturas.fecha_hasta','<=',$clause['fecha_vencimiento_hasta']);
                            $query->where("fac_facturas.formulario", "facturas_seguro");
                            
                        });

        $facturas->select("fac_facturas.id","fac_facturas.uuid_factura", "fac_facturas.codigo", "fac_facturas.fecha_desde", "fac_facturas.fecha_hasta", "fac_facturas.total", "cli_clientes.nombre", "fac_facturas.estado", "fac_facturas.centro_contable_id", "fac_facturas.cliente_id", "pol_polizas.numero", "pol_polizas.ramo", "pol_poliza_prima.sitio_pago");
        if ($sidx != NULL && $sord != NULL)
            $facturas->orderBy($sidx, $sord);
        if ($limit != NULL)
            $facturas->skip($start)->take($limit);
        //print_r($facturas->toSql());
        return $facturas->get();
    }

    public static function exportar($clause = array()) {

        $query = FacturaSeguro::where(function($query) use($clause) {
                    if (!empty($sidx) && preg_match("/cargo/i", $sidx)) {
                        $query->orderBy("nombre", $sord);
                    }
                });


        //Si existen variables de limite
        if ($clause != NULL && !empty($clause) && is_array($clause)) {
            foreach ($clause AS $field => $value) {
                $i = 0;
                foreach ($value AS $row) {

                    //$valor_fin[$i] = hex2bin($row);
                    $valor_fin[$i] = $row;

                    $i++;
                }
                //verificar si valor es array
                if (is_array($value)) {


                    //$query->whereIn("uuid_factura", $valor_fin);
                    $query->whereIn("id", $valor_fin);

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
        $factura = FacturaSeguro::find($ordenId);
        $comentario = new Comentario($comentarios);
        $factura->comentario_timeline()->save($comentario);
        return $factura;
    }
	
	public static function getFacturas($clause = array()) {
		$facturas=FacturaSeguro::select('fac_facturas.*','pol_polizas.ramo_id','seg_remesas_entrantes_facturas.chequeada')
		->where('fac_facturas.empresa_id',$clause['fac_facturas.empresa_id'])
		->whereIn('fac_facturas.estado',array('por_cobrar','cobrado_completo'))
		->where('formulario','facturas_seguro')
		->leftJoin("seg_remesas_entrantes_facturas", "seg_remesas_entrantes_facturas.factura_id", "=", "fac_facturas.id")
		->leftJoin("pol_polizas", "fac_facturas.id_poliza", "=", "pol_polizas.id")
		->leftJoin("seg_ramos", "pol_polizas.ramo_id", "=", "seg_ramos.id")
		->whereNull('seg_remesas_entrantes_facturas.factura_id');
		
		$fecha1="";
		$fecha2="";
		if(isset($clause["fecha1"]) && $clause["fecha1"]!=NULL && !empty($clause["fecha1"])){
			$fecha1=$clause["fecha1"];
			$facturas->whereRaw("DATE(fecha_desde) >= '".$clause["fecha1"]."'");
		}
		
		if(isset($clause["fecha2"]) && $clause["fecha2"]!=NULL && !empty($clause["fecha2"])){
			$fecha2=$clause["fecha2"];
			$facturas->whereRaw("DATE(fecha_desde) <= '".$clause["fecha2"]."'");
		}
		
		unset($clause["fecha1"]);
		unset($clause["fecha2"]);
		
		if($clause!=NULL && !empty($clause) && is_array($clause))
        {
                foreach($clause AS $field => $value)
                { 
					if($field=='ramo_id')
					{
						if($field=='ramo_id')
						{
							$facturas->whereIn($field, $clause["ramo_id"]);
						}
					}
					else{
						//verificar si valor es array
						if(is_array($value)){
								$facturas->where($field, $value[0], $value[1]);
						}else{
								$facturas->where($field, '=', $value);
						}
					}
                }
        }
		$facturas->orWhere(function($query) use($clause,$fecha1,$fecha2)
		{
			$query->where('fac_facturas.estado', 'cobrado_parcial')
			->where('fac_facturas.empresa_id',$clause['fac_facturas.empresa_id'])
			->where('formulario','facturas_seguro')
			->leftJoin("seg_remesas_entrantes_facturas", "seg_remesas_entrantes_facturas.factura_id", "=", "fac_facturas.id")
			->leftJoin("pol_polizas", "fac_facturas.id_poliza", "=", "pol_polizas.id")
			->leftJoin("seg_ramos", "pol_polizas.ramo_id", "=", "seg_ramos.id");
			
			if(isset($fecha1) && $fecha1!=NULL && !empty($fecha1) && $fecha1!=""){
				$query->whereRaw("DATE(fecha_desde) >= '".$fecha1."'");
			}
			
			if(isset($fecha2) && $fecha2!=NULL && !empty($fecha2) && $fecha2!=""){
				$query->whereRaw("DATE(fecha_desde) <= '".$fecha2."'");
			}
			
			if($clause!=NULL && !empty($clause) && is_array($clause))
			{
					foreach($clause AS $field => $value)
					{ 
						if($field=='ramo_id')
						{
							if($field=='ramo_id')
							{
								$query->whereIn($field, $clause["ramo_id"]);
							}
						}
						else{
							//verificar si valor es array
							if(is_array($value)){
									$query->where($field, $value[0], $value[1]);
							}else{
									$query->where($field, '=', $value);
							}
						}
					}
			}
		});
		
		$facturas->orderBy('seg_ramos.nombre')
		->groupBy('fac_facturas.id');
		;
		return $facturas->get();
    }
	
	public static function getFacturasRemesas($clause = array(),$clause1 = array()) {
		$facturas=FacturaSeguro::select('fac_facturas.*','pol_polizas.ramo_id','seg_remesas_entrantes_facturas.mont_pag_factura',
		'seg_remesas_entrantes_facturas.chequeada')
		->where('fac_facturas.empresa_id',$clause['fac_facturas.empresa_id'])
		->whereIn('fac_facturas.estado',array('por_cobrar','cobrado_completo'))
		->where('formulario','facturas_seguro')
		->leftJoin("seg_remesas_entrantes_facturas", "seg_remesas_entrantes_facturas.factura_id", "=", "fac_facturas.id")
		->leftJoin("pol_polizas", "fac_facturas.id_poliza", "=", "pol_polizas.id")
		->leftJoin("seg_ramos", "pol_polizas.ramo_id", "=", "seg_ramos.id")
		->whereNull('seg_remesas_entrantes_facturas.factura_id');
		
		$fecha1="";
		$fecha2="";
		if(isset($clause["fecha1"]) && $clause["fecha1"]!=NULL && !empty($clause["fecha1"])){
			$fecha1=$clause["fecha1"];
			$facturas->whereRaw("DATE(fecha_desde) >= '".$clause["fecha1"]."'");
		}
		
		if(isset($clause["fecha2"]) && $clause["fecha2"]!=NULL && !empty($clause["fecha2"])){
			$fecha2=$clause["fecha2"];
			$facturas->whereRaw("DATE(fecha_desde) <= '".$clause["fecha2"]."'");
		}
		
		unset($clause["fecha1"]);
		unset($clause["fecha2"]);
		
		if($clause!=NULL && !empty($clause) && is_array($clause))
        {
                foreach($clause AS $field => $value)
                {
					if($field=='ramo_id')
						{
							if($field=='ramo_id')
							{
								$facturas->whereIn($field, $clause["ramo_id"]);
							}
						}
						else{
							//verificar si valor es array
							if(is_array($value)){
									$facturas->where($field, $value[0], $value[1]);
							}else{
									$facturas->where($field, '=', $value);
							}
						}
                }
        }
		$facturas->orWhere(function($query) use ($clause,$fecha1,$fecha2)
		{
			$query->where('fac_facturas.estado', 'cobrado_parcial')
			->where('fac_facturas.empresa_id',$clause['fac_facturas.empresa_id'])
			->where('formulario','facturas_seguro')
			->leftJoin("seg_remesas_entrantes_facturas", "seg_remesas_entrantes_facturas.factura_id", "=", "fac_facturas.id")
			->leftJoin("pol_polizas", "fac_facturas.id_poliza", "=", "pol_polizas.id")
			->leftJoin("seg_ramos", "pol_polizas.ramo_id", "=", "seg_ramos.id");
			
			if(isset($fecha1) && $fecha1!=NULL && !empty($fecha1) && $fecha1!=""){
				$query->whereRaw("DATE(fecha_desde) >= '".$fecha1."'");
			}
			
			if(isset($fecha2) && $fecha2!=NULL && !empty($fecha2) && $fecha2!=""){
				$query->whereRaw("DATE(fecha_desde) <= '".$fecha2."'");
			}
			
			if($clause!=NULL && !empty($clause) && is_array($clause))
			{
				foreach($clause AS $field => $value)
				{ 
					if($field=='ramo_id')
					{
						if($field=='ramo_id')
						{
							$query->whereIn($field, $clause["ramo_id"]);
						}
					}
					else{
						//verificar si valor es array
						if(is_array($value)){
								$query->where($field, $value[0], $value[1]);
						}else{
								$query->where($field, '=', $value);
						}
					}
				}
			}
		});
		$facturas->orWhere(function($query) use($clause1,$clause,$fecha1,$fecha2)
		{
			$query->where('seg_remesas_entrantes_facturas.remesa_entrante_id', $clause1['remesa_entrante_id'])
			->where('formulario','facturas_seguro')
			->where('fac_facturas.empresa_id',$clause['fac_facturas.empresa_id'])
			->where('formulario','facturas_seguro')
			->leftJoin("seg_remesas_entrantes_facturas", "seg_remesas_entrantes_facturas.factura_id", "=", "fac_facturas.id")
			->leftJoin("pol_polizas", "fac_facturas.id_poliza", "=", "pol_polizas.id")
			->leftJoin("seg_ramos", "pol_polizas.ramo_id", "=", "seg_ramos.id");
			
			if(isset($fecha1) && $fecha1!=NULL && !empty($fecha1) && $fecha1!=""){
				$query->whereRaw("DATE(fecha_desde) >= '".$fecha1."'");
			}
			
			if(isset($fecha2) && $fecha2!=NULL && !empty($fecha2) && $fecha2!=""){
				$query->whereRaw("DATE(fecha_desde) <= '".$fecha2."'");
			}
			
			if($clause!=NULL && !empty($clause) && is_array($clause))
			{
				foreach($clause AS $field => $value)
				{ 
					if($field=='ramo_id')
					{
						if($field=='ramo_id')
						{
							$query->whereIn($field, $clause["ramo_id"]);
						}
					}
					else{
						//verificar si valor es array
						if(is_array($value)){
								$query->where($field, $value[0], $value[1]);
						}else{
								$query->where($field, '=', $value);
						}
					}
				}
			}
		});
		
		$facturas->orderBy('seg_ramos.nombre')
		->groupBy('fac_facturas.id');
		;
		return $facturas->get();
    }
	
	public static function getFacturasPrtocesadas($clause = array()) {
		$facturas=FacturaSeguro::select('fac_facturas.*','pol_polizas.ramo_id')
		->where('fac_facturas.empresa_id',$clause['fac_facturas.empresa_id'])
		->where('formulario','facturas_seguro')
		->leftJoin("seg_remesas_entrantes_facturas", "seg_remesas_entrantes_facturas.factura_id", "=", "fac_facturas.id")
		->leftJoin("pol_polizas", "fac_facturas.id_poliza", "=", "pol_polizas.id")
		->leftJoin("seg_ramos", "pol_polizas.ramo_id", "=", "seg_ramos.id");
		
		
		if($clause!=NULL && !empty($clause) && is_array($clause))
        {
                foreach($clause AS $field => $value)
                { 
					if($field=='fac_facturas.id' || $field=='fac_facturas.id1')
					{
						if($field=='fac_facturas.id')
						{
							$facturas->whereIn($field, $value);
						}
						if($field=='fac_facturas.id1')
						{
							$facturas->whereIn('fac_facturas.id', $value);
						}
					}
					else{
						//verificar si valor es array
						if(is_array($value)){
								$facturas->where($field, $value[0], $value[1]);
						}else{
								$facturas->where($field, '=', $value);
						}
					}
                }
        }
		
		$facturas->orderBy('seg_ramos.nombre')
		->groupBy('fac_facturas.id');
		;
		return $facturas->get();
    }
	
	public static function getFacturasPrtocesadasRemesas($clause = array()) {
		$facturas=FacturaSeguro::select('fac_facturas.*','pol_polizas.ramo_id','seg_remesas_entrantes_facturas.mont_pag_factura',
		'seg_remesas_entrantes_facturas.comision_pagada')
		->where('formulario','facturas_seguro')
		->leftJoin("seg_remesas_entrantes_facturas", "seg_remesas_entrantes_facturas.factura_id", "=", "fac_facturas.id")
		->leftJoin("pol_polizas", "fac_facturas.id_poliza", "=", "pol_polizas.id")
		->leftJoin("seg_ramos", "pol_polizas.ramo_id", "=", "seg_ramos.id")
		->where('seg_remesas_entrantes_facturas.remesa_entrante_id',$clause['remesa_entrante_id']);
		
		
		$facturas->orderBy('seg_ramos.nombre')
		->groupBy('fac_facturas.id');
		;
		return $facturas->get();
    }

    public function GetFacturasRemesasSalientes($id_factura,$id_aseguradora){
        $factura = FacturaSeguro::where("fac_facturas.id",$id_factura)->where('seg_aseguradoras.id',$id_aseguradora)->rightJoin("pol_polizas","pol_polizas.id","=","fac_facturas.id_poliza")->join("pol_poliza_prima","pol_poliza_prima.id_poliza","=","fac_facturas.id_poliza")->join("seg_aseguradoras","seg_aseguradoras.id","=","pol_polizas.aseguradora_id")->first();
        return  $factura;
    }
}
