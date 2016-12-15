<?php
namespace Flexio\Modulo\Documentos\Repository;
use Flexio\Modulo\Documentos\Models\Documentos as Documento;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Documentos\Transform\DocumentosTransformer as DocumentosTransformer;

class DocumentosRepository{

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
		//Si existen variables de limite
		if($clause!=NULL && !empty($clause) && is_array($clause))
		{
			foreach($clause AS $field => $value)
			{
				if($field == "orden_alquiler_id" || $field == "contrato_id"  || $field == "liquidacion_id" || $field == "factura_id" || $field == "pedido_id" ||   $field == "facturacompra_id"  || $field == "id" || $field == "ordencompra_id" || $field == "equipo_id" || $field == "intereses_asegurados_id_persona" || $field == "intereses_asegurados_id_vehiculo" || $field == "intereses_asegurados_id_casco_maritimo" || $field == "intereses_asegurados_id_articulo" || $field == "intereses_asegurados_id_ubicacion" || $field == "intereses_asegurados_id_casco_aereo" || $field == "intereses_asegurados_id_proyecto_actividad" || $field == "intereses_asegurados_id_carga" || $field == "id_cliente" || $field == "cotizacion_id" || $field == "ordenes_ventas_id" || $field == "proveedores_id" || $field == "items_id" || $field == "caja_id"){
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
					$query->where($field, $value[0], $value[1]);
				}else{
					$query->where($field, '=', $value);
				}
			}
		}

		//Si existen variables de orden
		if($sidx!=NULL && $sord!=NULL){
			if(!preg_match("/(estado)/i", $sidx)){
				$query->orderBy($sidx, $sord);
			}
		}

		//Si existen variables de limite
		if($limit!=NULL) $query->skip($start)->take($limit);
		return $query->get();
	}
}
