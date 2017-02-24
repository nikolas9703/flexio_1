<?php
namespace Flexio\Modulo\Documentos\Repository;
use Flexio\Modulo\Documentos\Models\Documentos as Documento;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Documentos\Transform\DocumentosTransformer as DocumentosTransformer;

class DocumentosRepository1{

	protected $ci;

	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct() {
		/*
		 * Instanciar codeigniter
		*/
		$this->ci = &get_instance();
	}

	function find($id) {
		return Documento::find($id);
	}



	function getAll($clause) {
		return Documento::where(function ($query) use($clause) {
			$query->where('empresa_id', '=', $clause['empresa_id']);
			if (!empty($clause['formulario']))
				$query->whereIn('formulario', $clause['formulario']);
			if (!empty($clause['estado']))
				$query->whereIn('estado', $clause['estado']);
		})->get();
	}

	function create($modeloInstancia=array(), $fieldset=array()) {

        $DocTransformer = new DocumentosTransformer;
		$documentos = $DocTransformer->crearInstancia($fieldset);

		$modeloInstancia->documentos()->saveMany($documentos);

		return $modeloInstancia;
	}

	public function createFacturasCompras($documentos=array()) {
         foreach($documentos AS $created){
             return Documento::create($created);
         }
	}

	function update($update) {
		return Documento::update($update);
	}

	function findByUuid($uuid) {
		return Documento::where('uuid_documento', hex2bin($uuid))->first();
	}

	public function delete($condicion) {
		return Documento::where(function($query) use($condicion) {
			$query->where('empresa_id', '=', $condicion ['empresa_id'] );
		})->delete();
	}

    public function get($clause, $sidx = NULL, $sord = NULL, $limit = NULL, $start = NULL)
    {
        $documentos = Documento::where(function($query) use ($clause){
            if(isset($clause['campo']) && is_array($clause['campo']) && !empty(array_filter($clause['campo']))){$query->deFiltro($clause['campo']);}
        });
        $documentos->where('padre_id', '=' , '0');
        $documentos->where('documentable_type', 'Flexio\Modulo\FacturasCompras\Models\FacturaCompra');
        if($sidx!=NULL && $sord!=NULL){$documentos->orderBy($sidx, $sord);}
        if($limit!=NULL){$documentos->skip($start)->take($limit);}
        return $documentos->get();
    }

    public function count($clause)
    {
        $documentos = Documento::where(function($query) use ($clause){
            if(isset($clause['campo']) && is_array($clause['campo']) && !empty(array_filter($clause['campo']))){$query->deFiltro($clause['campo']);}
        });

        return $documentos->count();
    }

	/**
	 * @function de listar y busqueda
	 */
	public function listar($clause = array(), $sidx = NULL, $sord = NULL, $limit = NULL, $start = NULL) {

		$query = Documento::with(array(
			'colaboradores',
			'pedidos',
			'ordenes_compra',
			'factura_compra',
			'factura_venta',
      'persona',
			'vehiculo_asegurados',
			'casco_maritimo',
			'articulo',
			'ubicacion',
			'casco_aereo',
			'proyecto',
			'carga',
			'cajas',
      'cliente',
      'cotizaciones',
      'ordenes_ventas',
			'ordenes_alquiler',
      'proveedores',
      'items',
	  'subcontratos',
	  'solicitudes',
			'polizas',
	  'movimientos_retiros',
      'subido_por'
		));

			//orden_alquiler_id

		//Filtros para cuando se muestra la tabla
		//como subpanel en otros modulos

		if(!empty($clause["contrato_id"])){
		        $query->orWhere("documentable_type", "Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquiler")->whereIn("documentable_id", array($clause["contrato_id"]));
		}
		if(!empty($clause["factura_id"])){
			$query->orWhere("documentable_type", "Flexio\Modulo\FacturasVentas\Models\FacturaVenta")->whereIn("documentable_id", array($clause["factura_id"]));
		}
		if(!empty($clause["pedido_id"])){
			$query->orWhere("documentable_type", "Flexio\Modulo\Pedidos\Models\Pedidos")->where("documentable_id", array($clause["pedido_id"]));
		}
		if(!empty($clause["facturacompra_id"])){
			$query->where(function($q) use ($clause){
				$q->where("documentable_type", "Flexio\Modulo\FacturasCompras\Models\FacturaCompra")->whereIn("documentable_id", array($clause["facturacompra_id"]));
			});
		}
		if(!empty($clause["ordencompra_id"])){

			$query->orWhere("documentable_type", "Flexio\Modulo\OrdenesCompra\Models\OrdenesCompra")->whereIn("documentable_id", array($clause["ordencompra_id"]));
		}
		if(!empty($clause["equipo_id"])){
			$query->orWhere("documentable_type", "Flexio\Modulo\Talleres\Models\EquipoTrabajo")->whereIn("documentable_id", array($clause["equipo_id"]));
		}
		if(!empty($clause["orden_alquiler_id"])){
			$query->orWhere("documentable_type", "Flexio\Modulo\OrdenesAlquiler\Models\OrdenVentaAlquiler")->whereIn("documentable_id", array($clause["orden_alquiler_id"]));
		}
                if(!empty($clause["intereses_asegurados_id_persona"])){
			$query->orWhere("documentable_type", "Flexio\Modulo\InteresesAsegurados\Models\InteresesPersonas")->whereIn("documentable_id", array($clause["intereses_asegurados_id_persona"]));
		}
                if(!empty($clause["intereses_asegurados_id_vehiculo"])){
			$query->orWhere("documentable_type", "Flexio\Modulo\InteresesAsegurados\Models\VehiculoAsegurados")->whereIn("documentable_id", array($clause["intereses_asegurados_id_vehiculo"]));
		}
                if(!empty($clause["intereses_asegurados_id_casco_maritimo"])){
			$query->orWhere("documentable_type", "Flexio\Modulo\InteresesAsegurados\Models\MaritimoAsegurados")->whereIn("documentable_id", array($clause["intereses_asegurados_id_casco_maritimo"]));
		}
                if(!empty($clause["intereses_asegurados_id_articulo"])){
			$query->orWhere("documentable_type", "Flexio\Modulo\InteresesAsegurados\Models\ArticuloAsegurados")->whereIn("documentable_id", array($clause["intereses_asegurados_id_articulo"]));
		}
                if(!empty($clause["intereses_asegurados_id_ubicacion"])){
			$query->orWhere("documentable_type", "Flexio\Modulo\InteresesAsegurados\Models\UbicacionAsegurados")->whereIn("documentable_id", array($clause["intereses_asegurados_id_ubicacion"]));
		}
                if(!empty($clause["intereses_asegurados_id_casco_aereo"])){
			$query->orWhere("documentable_type", "Flexio\Modulo\InteresesAsegurados\Models\AereoAsegurados")->whereIn("documentable_id", array($clause["intereses_asegurados_id_casco_aereo"]));
		}
                if(!empty($clause["intereses_asegurados_id_proyecto_actividad"])){
			$query->orWhere("documentable_type", "Flexio\Modulo\InteresesAsegurados\Models\ProyectoAsegurados")->whereIn("documentable_id", array($clause["intereses_asegurados_id_proyecto_actividad"]));
		}
                if(!empty($clause["intereses_asegurados_id_carga"])){
			$query->orWhere("documentable_type", "Flexio\Modulo\InteresesAsegurados\Models\CargaAsegurados")->whereIn("documentable_id", array($clause["intereses_asegurados_id_carga"]));
		}
                if(!empty($clause["id_cliente"])){
			$query->orWhere("documentable_type", "Flexio\Modulo\cliente\Models\Cliente")->whereIn("documentable_id", array($clause["id_cliente"]));
		}
                if(!empty($clause["cotizacion_id"])){
			$query->orWhere("documentable_type", "Flexio\Modulo\cotizaciones\Models\Cotizacion")->whereIn("documentable_id", array($clause["cotizacion_id"]));
		}
                if(!empty($clause["ordenes_ventas_id"])){
			$query->orWhere("documentable_type", "Flexio\Modulo\OrdenesVentas\Models\OrdenVenta")->whereIn("documentable_id", array($clause["ordenes_ventas_id"]));
		}
                if(!empty($clause["proveedores_id"])){
			$query->orWhere("documentable_type", "Flexio\Modulo\Proveedores\Models\Proveedores")->whereIn("documentable_id", array($clause["proveedores_id"]));

                }
                if(!empty($clause["items_id"])){
			$query->orWhere("documentable_type", "Flexio\Modulo\Inventarios\Models\Items")->whereIn("documentable_id", array($clause["items_id"]));
                }
      if(!empty($clause["caja_id"])){
				$query->orWhere("documentable_type", "Flexio\Modulo\Cajas\Models\Cajas")->whereIn("documentable_id", array($clause["caja_id"]));
      }
			if(!empty($clause["campo"])){
				$query->deFiltro($clause["campo"]);
			}
			if(!empty($clause["solicitud_id"])){
				$query->orWhere("documentable_type", "Flexio\Modulo\Solicitudes\Models\Solicitudes")->whereIn("documentable_id", array($clause["solicitud_id"]));
      }
      if(!empty($clause["poliza_id"])){
  			$query->orWhere("documentable_type", "Flexio\Modulo\Polizas\Models\Polizas")->whereIn("documentable_id", array($clause["poliza_id"]));
      }

			// comentado por conflicto
			// $query->deFiltro($clause["campo"]);
			// }

			if(!empty($clause["retiro_id"])){
			$query->orWhere("documentable_type", "Flexio\Modulo\MovimientosMonetarios\Models\MovimientosRetiros")->whereIn("documentable_id", array($clause["retiro_id"]));
      }
			unset($clause["campo"]);
		//Si existen variables de limite
		if($clause!=NULL && !empty($clause) && is_array($clause))
		{
			foreach($clause AS $field => $value)
			{
				if($field == "orden_alquiler_id" || $field == "contrato_id"  || $field == "liquidacion_id" || $field == "factura_id" || $field == "pedido_id" ||   $field == "facturacompra_id"  || $field == "id" || $field == "ordencompra_id" || $field == "equipo_id" || $field == "intereses_asegurados_id_persona" || $field == "intereses_asegurados_id_vehiculo" || $field == "intereses_asegurados_id_casco_maritimo" || $field == "intereses_asegurados_id_articulo" || $field == "intereses_asegurados_id_ubicacion" || $field == "intereses_asegurados_id_casco_aereo" || $field == "intereses_asegurados_id_proyecto_actividad" || $field == "intereses_asegurados_id_carga" || $field == "id_cliente" || $field == "cotizacion_id" || $field == "ordenes_ventas_id" || $field == "proveedores_id" || $field == "items_id" || $field == "caja_id" || $field == "retiro_id"){
					continue;
				}

				//Concatenar Nombre y Apellido para busqueda
				if($field == "nombre"){
					$field = Capsule::raw("CONCAT_WS(' ', IF(nombre != '', nombre, ''), IF(apellido != '', apellido, ''))");
				}

				//Verificar si el campo tiene el simbolo @ y removerselo.
				if(preg_match('/@/i', $field)){
					$field = str_replace("@", "", $field);
				}

				//verificar si valor es array
				if(is_array($value)){
					//Concatenar Nombre y Apellido para busqueda
					if($field == "subido_por"){
						$query->join('usuarios', 'doc_documentos.subido_por', '=', 'usuarios.id');
						$query->where('usuarios.nombre',$value[0], $value[1]);
					}
					else{
					$query->where($field, $value[0], $value[1]);
				}
				}else{
					$query->where($field, '=', $value);
				}
			}
		}
		//Si existen variables de orden
		if($sidx!=NULL && $sord!=NULL){
			if(!preg_match("/(estado)/i", $sidx)){
				if($sidx=='no_accion')
				{
					$sidx="nombre_documento";
				}
				if($sidx=='tipo')
				{
					$sidx="archivo_nombre";
				}
				if($sidx=='accionable_type')
				{
					$sidx='created_at';
				}
				if($sidx=='colaborador_id')
				{
					$sidx='subido_por';
				}
				$query->orderBy($sidx, $sord);
			}
		}

		//Si existen variables de limite
		if($limit!=NULL) $query->skip($start)->take($limit);
		return $query->get();
	}

	public function listar_seguros($clause = array(), $sidx = NULL, $sord = NULL, $limit = NULL, $start = NULL,$clause2=array()) {

		$query = Documento::with(array(
			//'colaboradores',
			'pedidos',
			'ordenes_compra',
			'factura_compra',
			'factura_venta',
			'persona',
			'vehiculo_asegurados',
			'casco_maritimo',
			'articulo',
			'ubicacion',
			'casco_aereo',
			'proyecto',
			'carga',
			'cajas',
			'cliente',
			'cotizaciones',
			'ordenes_ventas',
			'ordenes_alquiler',
			'proveedores',
			'items',
			'subido_por',
			'solicitudes',
			'polizas',
			'fac_facturas'
		));
			//orden_alquiler_id

		//Filtros para cuando se muestra la tabla
		//como subpanel en otros modulos

		if(!empty($clause["contrato_id"])){
		        $query->orWhere("documentable_type", "Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquiler")->whereIn("documentable_id", array($clause["contrato_id"]));
		}
		if(!empty($clause["factura_id"])){
			$query->orWhere("documentable_type", "Flexio\Modulo\FacturasVentas\Models\FacturaVenta")->whereIn("documentable_id", array($clause["factura_id"]));
		}
		if(!empty($clause["pedido_id"])){
			$query->orWhere("documentable_type", "Flexio\Modulo\Pedidos\Models\Pedidos")->where("documentable_id", array($clause["pedido_id"]));
		}
		if(!empty($clause["facturacompra_id"])){
			$query->orWhere("documentable_type", "Flexio\Modulo\FacturasCompras\Models\FacturaCompra")->whereIn("documentable_id", array($clause["facturacompra_id"]));
		}
		if(!empty($clause["ordencompra_id"])){

			$query->orWhere("documentable_type", "Flexio\Modulo\OrdenesCompra\Models\OrdenesCompra")->whereIn("documentable_id", array($clause["ordencompra_id"]));
		}
		if(!empty($clause["equipo_id"])){
			$query->orWhere("documentable_type", "Flexio\Modulo\Talleres\Models\EquipoTrabajo")->whereIn("documentable_id", array($clause["equipo_id"]));
		}
		if(!empty($clause["orden_alquiler_id"])){
			$query->orWhere("documentable_type", "Flexio\Modulo\OrdenesAlquiler\Models\OrdenVentaAlquiler")->whereIn("documentable_id", array($clause["orden_alquiler_id"]));
		}
                if(!empty($clause["intereses_asegurados_id_persona"])){
			$query->orWhere("documentable_type", "Flexio\Modulo\InteresesAsegurados\Models\InteresesPersonas")->whereIn("documentable_id", array($clause["intereses_asegurados_id_persona"]));
		}
                if(!empty($clause["intereses_asegurados_id_vehiculo"])){
			$query->orWhere("documentable_type", "Flexio\Modulo\InteresesAsegurados\Models\VehiculoAsegurados")->whereIn("documentable_id", array($clause["intereses_asegurados_id_vehiculo"]));
		}
                if(!empty($clause["intereses_asegurados_id_casco_maritimo"])){
			$query->orWhere("documentable_type", "Flexio\Modulo\InteresesAsegurados\Models\MaritimoAsegurados")->whereIn("documentable_id", array($clause["intereses_asegurados_id_casco_maritimo"]));
		}
                if(!empty($clause["intereses_asegurados_id_articulo"])){
			$query->orWhere("documentable_type", "Flexio\Modulo\InteresesAsegurados\Models\ArticuloAsegurados")->whereIn("documentable_id", array($clause["intereses_asegurados_id_articulo"]));
		}
                if(!empty($clause["intereses_asegurados_id_ubicacion"])){
			$query->orWhere("documentable_type", "Flexio\Modulo\InteresesAsegurados\Models\UbicacionAsegurados")->whereIn("documentable_id", array($clause["intereses_asegurados_id_ubicacion"]));
		}
                if(!empty($clause["intereses_asegurados_id_casco_aereo"])){
			$query->orWhere("documentable_type", "Flexio\Modulo\InteresesAsegurados\Models\AereoAsegurados")->whereIn("documentable_id", array($clause["intereses_asegurados_id_casco_aereo"]));
		}
                if(!empty($clause["intereses_asegurados_id_proyecto_actividad"])){
			$query->orWhere("documentable_type", "Flexio\Modulo\InteresesAsegurados\Models\ProyectoAsegurados")->whereIn("documentable_id", array($clause["intereses_asegurados_id_proyecto_actividad"]));
		}
                if(!empty($clause["intereses_asegurados_id_carga"])){
			$query->orWhere("documentable_type", "Flexio\Modulo\InteresesAsegurados\Models\CargaAsegurados")->whereIn("documentable_id", array($clause["intereses_asegurados_id_carga"]));
		}
                if(!empty($clause["id_cliente"])){
			$query->orWhere("documentable_type", "Flexio\Modulo\cliente\Models\Cliente")->whereIn("documentable_id", array($clause["id_cliente"]));
		}
                if(!empty($clause["cotizacion_id"])){
			$query->orWhere("documentable_type", "Flexio\Modulo\cotizaciones\Models\Cotizacion")->whereIn("documentable_id", array($clause["cotizacion_id"]));
		}
                if(!empty($clause["ordenes_ventas_id"])){
			$query->orWhere("documentable_type", "Flexio\Modulo\OrdenesVentas\Models\OrdenVenta")->whereIn("documentable_id", array($clause["ordenes_ventas_id"]));
		}
                if(!empty($clause["proveedores_id"])){
			$query->orWhere("documentable_type", "Flexio\Modulo\Proveedores\Models\Proveedores")->whereIn("documentable_id", array($clause["proveedores_id"]));

                }
                if(!empty($clause["items_id"])){
			$query->orWhere("documentable_type", "Flexio\Modulo\Inventarios\Models\Items")->whereIn("documentable_id", array($clause["items_id"]));
                }
                if(!empty($clause["caja_id"])){
			$query->orWhere("documentable_type", "Flexio\Modulo\Cajas\Models\Cajas")->whereIn("documentable_id", array($clause["caja_id"]));
                }
				if(!empty($clause["solicitud_id"])){
			$query->orWhere("documentable_type", "Flexio\Modulo\Solicitudes\Models\Solicitudes")->whereIn("documentable_id", array($clause["solicitud_id"]));
                }
                if(!empty($clause["poliza_id"])){
            $query->orWhere("documentable_type", "Flexio\Modulo\Polizas\Models\Polizas")->whereIn("documentable_id", array($clause["poliza_id"]));
                }

				if(!empty($clause["factura_seguros_id"])){
            $query->orWhere("documentable_type", "Flexio\Modulo\FacturasSeguros\Models\FacturaSeguro")->whereIn("documentable_id", array($clause["factura_seguros_id"]));
                }

		//Si existen variables de limite
		if($clause2!=NULL && !empty($clause2) && is_array($clause2))
		{
			foreach($clause2 AS $field => $value)
			{
				/*if($field == "orden_alquiler_id" || $field == "contrato_id"  || $field == "liquidacion_id" || $field == "factura_id" || $field == "pedido_id" ||   $field == "facturacompra_id"  || $field == "id" || $field == "ordencompra_id" || $field == "equipo_id" || $field == "intereses_asegurados_id_persona" || $field == "intereses_asegurados_id_vehiculo" || $field == "intereses_asegurados_id_casco_maritimo" || $field == "intereses_asegurados_id_articulo" || $field == "intereses_asegurados_id_ubicacion" || $field == "intereses_asegurados_id_casco_aereo" || $field == "intereses_asegurados_id_proyecto_actividad" || $field == "intereses_asegurados_id_carga" || $field == "id_cliente" || $field == "cotizacion_id" || $field == "ordenes_ventas_id" || $field == "proveedores_id" || $field == "items_id" || $field == "caja_id"){
					continue;
				}*/
				//Verificar si el campo tiene el simbolo @ y removerselo.
				if(preg_match('/@/i', $field)){
					$field = str_replace("@", "", $field);
				}

				//verificar si valor es array
				if(is_array($value)){
					//Concatenar Nombre y Apellido para busqueda
					if($field == "subido_por"){
						$query->join('usuarios', 'doc_documentos.subido_por', '=', 'usuarios.id');
						$query->where('usuarios.nombre',$value[0], $value[1]);
					}
					else{
						$query->where($field, $value[0], $value[1]);
						/*if($field=='nombre_documento')
						{
							$query->orwhere('archivo_nombre', $value[0], $value[1]);
						}*/
					}
				}else{
					$query->where($field, '=', $value);
				}
			}
		}

		//Si existen variables de orden
		if($sidx!=NULL && $sord!=NULL){
			if(!preg_match("/(estado)/i", $sidx)){
				if($sidx=='no_accion')
				{
					$sidx="nombre_documento";
				}
				if($sidx=='tipo')
				{
					$sidx="archivo_nombre";
				}
				if($sidx=='accionable_type')
				{
					$sidx='created_at';
				}
				if($sidx=='colaborador_id')
				{
					$sidx='subido_por';
				}
				$query->orderBy($sidx, $sord);
			}
		}

		//Si existen variables de limite
		if($limit!=NULL) $query->skip($start)->take($limit);

		return $query->get();
	}
	public static function descargar($clause = array()) {
				 	$documentos = [];
	        if ($clause != NULL && !empty($clause) && is_array($clause)) {
							$documentos = documento::whereIn('id', $clause['id'])->get();
					}
					return $documentos;
			}
	public function exportar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {
	//filtros
        $documentos = Documento::whereIn('id',$clause["id"]);

		return $documentos->get();
	}
}
