<?php
namespace Flexio\Jobs\ContratosAlquiler;

use Carbon\Carbon;
use Flexio\Jobs\ContratosAlquiler\CargosAnticipadosInterface;
use Flexio\Modulo\ContratosAlquiler\Models\CargosAlquiler AS CargosAlquilerModel;
use Flexio\Jobs\ContratosAlquiler\CronCortesFacturacionAlquiler;

class CargosAnticipados extends Cargos implements CargosAnticipadosInterface {

	public $CronCortesFacturacionAlquiler;
	public $fecha_entrega;
	public $empresa_id;
	public $entrega_id;
	public $items;
	public $dia_corte;
	public $corte_facturacion;
	public $facturar_contra_entrega;
	public $fecha_ejecutar_cargo;
	public $lapso_tiempo_facturacion;
	public $fecha_orden_venta;
	public $ultimo_cargo;

	public function __construct() {
		$this->CronCortesFacturacionAlquiler = new CronCortesFacturacionAlquiler();
	}

	/**
	 * Esta funcion calcula y ejecuta la
	 * cantidad de cargos que hay que generar
	 * hasta la fecha de corte de facturacion.
	 *
	 * @param  array  $item [description]
	 * @return [type]       [description]
	 */
	public function calcular($entrega) {

		//Entrega Info
		//$fecha_entrega 						= Carbon::parse($entrega->fecha_entrega);
		$this->fecha_entrega 						= !empty($entrega["fecha_entrega"]) ? $entrega["fecha_entrega"] : "";
		$this->empresa_id 							= !empty($entrega["empresa_id"]) ? $entrega["empresa_id"] : "";
		$this->entrega_id 							= !empty($entrega["entrega_id"]) ? $entrega["entrega_id"] : "";
		$this->items 										= !empty($entrega["items"]) ? $entrega["items"] : array();
		$this->facturar_contra_entrega 	= !empty($entrega["facturar_contra_entrega"]) ? $entrega["facturar_contra_entrega"] : "";
		$this->corte_facturacion 				= !empty($entrega["corte_facturacion"]) ? str_replace("í","i",str_replace("í","i",str_replace(" ","_",strtolower($entrega["corte_facturacion"])))) : "";
		$this->dia_corte 								= !empty($entrega["dia_corte"]) ? $entrega["dia_corte"] : "";

		//Fecha actual
		$fecha_actual = Carbon::now('America/Panama');

		//Verificar si existe el corte seleccionado
		if(empty($this->periodo_facturacion[$this->corte_facturacion]) || Carbon::parse($this->fecha_entrega->format('Y-m-d'))->lt( Carbon::parse(Carbon::parse($fecha_actual)->format('Y-m-d')) )){
			return false;
		}

		//Fecha de Corte de Facturacion
		$this->lapso_tiempo_facturacion = $this->periodo_facturacion[$this->corte_facturacion]["lapso"];

		//------------------------------------------
		// Para Contrato de Alquiler con Corte de Facturacion
		// igual a Periodo de Dias.
		// ---
		// El lapso de tiempo de facturacion va ser igual a valor del
		// campo (Corte de facturacion) de formulario
		// Contrato de Alquiler
		//------------------------------------------
		if(preg_match('/periodo/i', $this->corte_facturacion)){
			$this->lapso_tiempo_facturacion = $this->dia_corte;
		}

		$count=1;
		$total_items=count($this->items);
		foreach($this->items AS $item) {

			$item_id 			= !empty($item["item_id"]) ? $item["item_id"] : "";
			$ciclo 				= !empty($item["ciclo"]) ? $item["ciclo"] : "";
			$ciclo_id 		= !empty($item["ciclo_id"]) ? $item["ciclo_id"] : "";
			$contrato_id 	= !empty($item["contrato_id"]) ? $item["contrato_id"] : "";

			//------------------------------------------
			// Seleccionar el ultimo cargo realizado a el
			// item de esta entrega.
			//------------------------------------------
			$clause = array(
				"cargoable_id" 		=> $this->entrega_id,
				"item_id" 				=> $item_id,
				"empresa_id" 			=> $this->empresa_id,
				"ciclo_id" 				=> $ciclo_id,
				"cargoable_type" 	=> "Flexio\Modulo\EntregasAlquiler\Models\EntregasAlquiler",
			);
			$this->ultimo_cargo = $this->ultimoCargo($clause);
			$this->fecha_ejecutar_cargo = !empty($this->ultimo_cargo) ? Carbon::parse($this->ultimo_cargo->fecha_cargo)->copy() : Carbon::parse($entrega["fecha_entrega"])->copy();

			//Verificar si el cargo ya fue ejecutado
			//para la la fecha de cargo actual
			$clause["fecha_cargo"] = Carbon::parse((!empty($this->ultimo_cargo) ? $this->ultimo_cargo->fecha_cargo : $this->fecha_entrega));
			$check = CargosAlquilerModel::clauseFiltro($clause)->get()->last();

			//Si ya existe el cargo
			//salstar a la siguiente
			//iteracion.
			if(collect($check)->toArray()){
				continue;
			}

			//Calcular Fecha de Ejecucion de orden de Venta
			$dia_corte = !empty($this->dia_corte) ? str_pad($this->dia_corte, 2, "0", STR_PAD_LEFT) : "01";

			//Calcular la fecha de la orden segun ultimo cargo o fecha de entrega
			$fechaordenventa = !empty($this->ultimo_cargo->fecha_cargo) ? Carbon::parse($this->fecha_ejecutar_cargo)->{$this->periodo_facturacion[$this->corte_facturacion]["func_addition"]}($this->lapso_tiempo_facturacion) : Carbon::parse($this->fecha_entrega)->{$this->periodo_facturacion[$this->corte_facturacion]["func_addition"]}($this->lapso_tiempo_facturacion);

			//Si corte mensual, fecha de orden es segun dia de corte seleccionado
			$this->fecha_orden_venta = preg_match('/mensual/i', $this->corte_facturacion) ? $fechaordenventa->format("Y-m-$dia_corte H:i:s") : $fechaordenventa;

			//Calcular Cantidad de Cargos
			$cantidad_cargos = preg_match('/mensual/i', $ciclo) ? 1 : Carbon::parse($this->fecha_entrega)->{$this->periodo[$ciclo]["func_difference"]}(Carbon::parse($this->fecha_orden_venta));

			//Recorrer Cantidad Cargos
			for ($i=0; $i<$cantidad_cargos; $i++) {
				$this->registrar($item);
			}

			//Si ya se recorrio todos los items
			if($total_items == $count){
				sleep(2);
				//Generar Orden de Venta de los cargos anticipados creados
				$this->CronCortesFacturacionAlquiler->ejecutar();
			}

			$count++;
		}
	}

	public function registrar($item=array()) {

			$ciclo = !empty($item["ciclo"]) ? $item["ciclo"] : "";

			//------------------------------------------
			// Si no existe periodo, continuar
			// siguiente iteracion
			//------------------------------------------
			if(empty($this->periodo[$ciclo])){
				continue;
			}

			$item_id 		= !empty($item["item_id"]) ? $item["item_id"] : "";
			$cantidad 	= !empty($item["cantidad"]) ? $item["cantidad"] : "";
			$series 		= !empty($item["series"]) ? $item["series"] : array();
			$devoluciones	= !empty($item["devoluciones"]) ? $item["devoluciones"] : array();
			$contrato_id = !empty($item["contrato_id"]) ? $item["contrato_id"] : "";

			//eliminar estos valores del ArrayObject
			unset($item["series"]);
			unset($item["devoluciones"]);

			$item["cargoable_id"] = $this->entrega_id;
			$item["cargoable_type"] = "Flexio\Modulo\EntregasAlquiler\Models\EntregasAlquiler";

			//Lapso de tiempo a calcular
			$lapso_tiempo = $this->periodo[$ciclo]["lapso"];

			//------------------------------------------
			// Fecha real de cargo
			//------------------------------------------
			$fecha_cargo = $this->fecha_ejecutar_cargo->{$this->periodo[$ciclo]["func_addition"]}($lapso_tiempo);

			//Para validar que cada cargo solo se ejecute
			//una sola vez en el dia/mes correspondiente
			if(preg_match("/diario/i", $this->corte_facturacion)){
				$check_fecha_orden_venta = preg_match('/mensual|6|15|28|30/i', $ciclo) ? $this->fecha_ejecutar_cargo->{$this->periodo_facturacion[$ciclo]["func_addition"]}(1) : $this->fecha_orden_venta;
			}else{
				$check_fecha_orden_venta = $this->fecha_orden_venta;
			}

			if(preg_match("/hora|6|15|28|30/i", $ciclo)){
				if(Carbon::parse($this->fecha_ejecutar_cargo->format('Y-m-d H:i:s'))->timestamp > Carbon::parse($check_fecha_orden_venta->format('Y-m-d H:i:s'))->timestamp) {
					return false;
				}
			}

			//Fecha Real para Corte de Facturacion diario
			if(preg_match("/diario/i", $this->corte_facturacion)){
				$this->fecha_orden_venta = Carbon::parse($this->fecha_entrega)->{$this->periodo_facturacion[$this->corte_facturacion]["func_addition"]}($this->lapso_tiempo_facturacion);
			}

			//------------------------------------------
			// Armar array item
			// para items no serializados/serializados
			// con cantidad mayor a 1
			//------------------------------------------
			$item = $this->preparar($item, $cantidad, $series, $devoluciones, $this->empresa_id, $fecha_cargo);

			//------------------------------------------
			// Guardar en DB cargo
			//------------------------------------------
			$this->guardar($this->entrega_id, $item, $item_id, $this->empresa_id);
	}
}
