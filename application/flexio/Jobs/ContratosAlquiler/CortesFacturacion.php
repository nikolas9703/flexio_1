<?php
namespace Flexio\Jobs\ContratosAlquiler;

use Carbon\Carbon;
use Flexio\Library\Util\Utiles;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\EntregasAlquiler\Models\EntregasAlquiler;
use Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquiler;
use Flexio\Modulo\ContratosAlquiler\Models\CargosAlquiler AS CargosAlquilerModel;
use Flexio\Modulo\ContratosAlquiler\Repository\CargosRepository;
use Flexio\Modulo\OrdenesAlquiler\Models\OrdenVentaAlquiler;
use Flexio\Modulo\OrdenesAlquiler\Models\OrdenVentaAlquilerCatalogo;
use Flexio\Modulo\OrdenesAlquiler\Repository\OrdenVentaAlquilerRepository;

class CortesFacturacion implements CortesFacturacionInterface {

	public $periodo = array(
		"diario" => array(
			"lapso" => 1,
			"func_difference" => "diffInDays",
			"func_addition" => "addDays",
		),
		"mensual" => array(
			"lapso" => 1,
			"func_difference" => "diffInMonths",
			"func_addition" => "addMonths",
			"func_prorrateo" => "diffInDays",
			"divisor_prorrateo" => 30
		),
		"periodo_de_dias" => array(
			"lapso" => 0, //El lapso de tiempo va ser igual a valor del campo (Corte de facturacion) de formulario Contrato de Alquiler
			"func_difference" => "diffInDays",
			"func_addition" => "addDays",
			"func_prorrateo" => "diffInDays",
			"divisor_prorrateo" => 7
		),
	);

	protected $CargosRepository;
	protected $OrdenVentaAlquilerRepository;
	protected $facturar_contra_entrega;

	public function __construct(array $attributes = array()) {
		$this->CargosRepository = new CargosRepository();
		$this->OrdenVentaAlquilerRepository = new OrdenVentaAlquilerRepository();
	}

	/**
	 * Registra las orden de venta de alquiler
	 * por contrato.
	 *
	 * @param  array  $items Arreglo de items de la entrega
	 * @return void
	 */
	public function verificar($contrato=NULL) {

		//Contrato Info
		$corte_facturacion 	= !empty($contrato["corte_facturacion"]) ? str_replace("í","i",str_replace("í","i",str_replace(" ","_",strtolower($contrato["corte_facturacion"])))) : "";
		$dia_corte 			= !empty($contrato["dia_corte"]) ? $contrato["dia_corte"] : "";
		$this->facturar_contra_entrega = !empty($contrato["facturar_contra_entrega"]) ? $contrato["facturar_contra_entrega"] : "";

		//------------------------------------------
		// Si no existe periodo, continuar
		// siguiente iteracion
		//------------------------------------------
		if(empty($this->periodo[$corte_facturacion])){
			return false;
		}

		//------------------------------------------
		// Los cortes de facturacion
		// corren en base a la fecha de entrega
		//------------------------------------------
		$fecha_entrega 	= Carbon::parse($contrato["fecha_entrega"])->copy();

		//Fecha actual
		$fecha_actual = Carbon::now('America/Panama');

		//Lapso de tiempo a calcular
		$lapso_tiempo = $this->periodo[$corte_facturacion]["lapso"];

		//------------------------------------------
		// Para Contrato de Alquiler con Corte de Facturacion
		// igual a Periodo de Dias.
		// ---
		// El lapso de tiempo va ser igual a valor del
		// campo (Corte de facturacion) de formulario
		// Contrato de Alquiler
		//------------------------------------------
		if(preg_match('/periodo/i', $corte_facturacion)){
			$lapso_tiempo = $dia_corte;
		}

		//------------------------------------------
		// Calcular el Tiempo Transcurrido entre
		// Fecha a ejecultar VS fecha/hora actual
		//------------------------------------------
		$tiempo_transcurrido = $fecha_entrega->{$this->periodo[$corte_facturacion]["func_difference"]}($fecha_actual);

		//------------------------------------------
		// Sumarle lapso a $fecha_ejecutar_cargo
		// para obtener la fecha actual de la orden de venta.
		//------------------------------------------
		$fecha_orden_venta = $fecha_entrega->{$this->periodo[$corte_facturacion]["func_addition"]}($lapso_tiempo);

		//Si corte mensual, fecha de orden es segun dia de corte seleccionado
		$fecha_orden_venta = preg_match('/mensual/i', $corte_facturacion) && !empty($dia_corte) ? Carbon::parse($fecha_orden_venta->format("Y-m-". str_pad($dia_corte, 2, "0", STR_PAD_LEFT) ." H:i:s"))->copy() : $fecha_orden_venta;

		//------------------------------------------
		// Verificar si el Contrato de Alquiler
		// tiene opcion "Facturar contra entrega"
		// igual a Si.
		//------------------------------------------
		if(preg_match("/si/i", $this->facturar_contra_entrega)){

			// Registrar Cargos a Items
			$this->preparar($contrato, $fecha_orden_venta);
		}

		//------------------------------------------
		// Verificar si Tiempo Transucrrido
		// es IGUAL a Lapso de Tiempo
		//------------------------------------------
		else if($tiempo_transcurrido >= $lapso_tiempo) {

			//------------------------------------------
			// Guardar en DB cargo
			//------------------------------------------
			$this->preparar($contrato, $fecha_orden_venta);
		}
	}

	/**
	 * Guardar el registro de Orden de Venta de Alquiler.
	 *
	 * @param  integer $entrega_id [description]
	 * @param  array   $item       [description]
	 * @return void
	 */
	public function preparar($contrato=NULL, $fecha_orden_venta=NULL) {

		if($contrato==NULL){
			return false;
		}

		//Contrato Info
		$centro_contable_id 	= !empty($contrato["centro_contable_id"]) ? $contrato["centro_contable_id"] : "";
		$centro_facturacion_id 	= !empty($contrato["centro_facturacion_id"]) ? $contrato["centro_facturacion_id"] : "";
		$creado_por 				= !empty($contrato["creado_por"]) ? $contrato["creado_por"] : "";
		$cliente_id 				= !empty($contrato["cliente_id"]) ? $contrato["cliente_id"] : "";
		$corte_facturacion_id 	= !empty($contrato["corte_facturacion_id"]) ? $contrato["corte_facturacion_id"] : "";
		$corte_facturacion 	= !empty($contrato["corte_facturacion"]) ? str_replace("í","i",str_replace(" ","_",strtolower($contrato["corte_facturacion"]))) : "";
		$dia_corte 			= !empty($contrato["dia_corte"]) ? $contrato["dia_corte"] : "";
		$cargos 				= !empty($contrato["cargos"]) ? $contrato["cargos"] : "";
		$fecha_entrega 	= Carbon::parse($contrato["fecha_entrega"]);
		$empresa_id 		= !empty($contrato["empresa_id"]) ? $contrato["empresa_id"] : "";
		$contrato_id 		= !empty($contrato["contrato_id"]) ? $contrato["contrato_id"] : "";
		$lista_precio_alquiler_id 		= !empty($contrato["lista_precio_alquiler_id"]) ? $contrato["lista_precio_alquiler_id"] : "";
		$items_alquiler = !empty($contrato["items"]) ? $contrato["items"] : array();
		$fecha_desde 		= $fecha_orden_venta->format('d/m/Y');
		$fecha_hasta 		= $fecha_orden_venta->addMonth()->format('d/m/Y');

		$termino_pago = OrdenVentaAlquilerCatalogo::where('tipo', '=', 'termino_pago')->where('etiqueta', 'LIKE', '%contado%')->first();
		$estado = OrdenVentaAlquilerCatalogo::where('tipo', '=', 'etapa')->where('etiqueta', 'LIKE', '%por_facturar%')->first();

		//------------------------------------------
		// Verificar si el Contrato de Alquiler
		// tiene opcion "Facturar contra entrega"
		// igual a Si.
		//------------------------------------------
		if(preg_match("/si/i", $this->facturar_contra_entrega)){
			$fecha_desde = Carbon::now('America/Panama')->format('d/m/Y');
			$fecha_hasta = Carbon::now('America/Panama')->addMonth()->format('d/m/Y');
		}

		$fieldset = array(
			"empresa_id" 						=> $empresa_id,
			"cliente_id" 						=> $cliente_id,
			"contrato_id" 					=> $contrato_id,
			"termino_pago" 					=> $termino_pago->etiqueta,
			"fecha_desde" 					=> $fecha_desde,
			"fecha_hasta" 					=> $fecha_hasta,
			"creado_por" 						=> $creado_por,
			"created_by" 						=> $creado_por,
			"centro_contable_id" 		=> $centro_contable_id,
			"centro_facturacion_id" => $centro_facturacion_id,
			"lista_precio_alquiler_id" => $lista_precio_alquiler_id,
			"estado" 								=> $estado->etiqueta,
			"formulario" 						=> "orden_alquiler",
		);

		$i=0;
		$items = array();
		$descuento = 0;
		$impuesto = 0;
		foreach ($items_alquiler as $item) {

			$descuento += ($item["precio_total"]*$item["descuento"])/100;
			$impuesto += (($item["precio_total"]-$descuento)*$item["impuesto"])/100;

			$items[$i]["item_id"] 						= $item["item"]["id"];
			$items[$i]["cantidad"] 						= $item["cantidad"];
			$items[$i]["empresa_id"] 					= $empresa_id;
			$items[$i]["tarifa_fecha_desde"] 	= $item["tarifa_fecha_desde"];
			$items[$i]["tarifa_fecha_hasta"] 	= $item["tarifa_fecha_hasta"];
			$items[$i]["tarifa_pactada"] 			= $item["tarifa_pactada"];
			$items[$i]["tarifa_periodo_id"] 	= $item["periodo"]["id"];
			$items[$i]["tarifa_monto"] 				= $item["tarifa_monto"];
			$items[$i]["tarifa_cantidad_periodo"] = $item["tarifa_cantidad_periodo"];
			$items[$i]["precio_total"] 				= $item["tarifa_monto"]*$item["tarifa_cantidad_periodo"];
			$items[$i]["categoria"] 					= $item["categoria_id"];
			$items[$i]["impuesto_total"] 			= $item["impuesto_total"];
			$items[$i]["impuesto"] 						= $item["impuesto_id"];
			//$items[$i]["impuesto_id"] 				= $item["impuesto_id"];
			//$items[$i]["impuesto"] 						= $item["impuesto"];
			$items[$i]["descuento"] 					= $item["descuento"];
			$items[$i]["descuento_total"] 		= $descuento;
			$items[$i]["cuenta"] 							= $item["cuenta_id"];
			$items[$i]["atributo_id"] 				= $item["atributo_id"];
			$items[$i]["atributo_text"] 			= $item["atributo_text"];
			$items[$i]["precio_unidad"] 			= $item["tarifa_monto"];
			$items[$i]["id_pedido_item"] 			= "";
			$i++;
		}

		$subtotal = collect($items_alquiler)->sum('precio_total');
		$fieldset["subtotal"] = $subtotal;
		$fieldset["descuento"] = $descuento;
		$fieldset["impuestos"] = $impuesto;
		$fieldset["total"] = ($subtotal + $impuesto) - $descuento;

		//Guardar Orden de Venta
		$this->guardar($fieldset, $items, $cargos);
	}

	public function guardar($fieldset, $items, $cargos) {

		Capsule::beginTransaction();
		try {

				$total = $this->OrdenVentaAlquilerRepository->lista_totales(['empresa_id' => $fieldset["empresa_id"]]);
				$year = Carbon::now()->format('y');
				$codigo = Utiles::generar_codigo("", $total + 1);
				$fieldset['codigo'] = $codigo;

				$data = array('ordenalquiler' => $fieldset, 'lineitem' => $items);
				$modelOrdenVenta = $this->OrdenVentaAlquilerRepository->create($data);

		} catch (Illuminate\Database\QueryException $e) {
				Capsule::rollback();
		}

		if (!empty($modelOrdenVenta)) {
			Capsule::commit();
			//Cambiar estado de cargos facturados
			CargosAlquilerModel::whereIn("id", $cargos)->update(array("estado" => "facturado"));
		}
	}
}
