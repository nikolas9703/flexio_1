<?php
namespace Flexio\Jobs\ContratosAlquiler;

use Carbon\Carbon;
use Flexio\Library\Util\Utiles;
use Flexio\Jobs\ContratosAlquiler\CargosProgramadosInterface;
use Flexio\Modulo\ContratosAlquiler\Models\CargosAlquiler AS CargosAlquilerModel;

class CargosProgramados extends Cargos implements CargosProgramadosInterface {

	public $fecha_entrega;
	public $fecha_ejecutar_cargo;
	public $fecha_actual;
	public $empresa_id;
	public $entrega_id;
	public $items;
	public $ultimo_cargo;
	public $calculo_costo_retorno;
	public $lista_precio_alquiler_id;

	public function __construct() {}

	/**
	 * Registra los cargos a cada Item
	 *
	 * @param  array  $items Arreglo de items de la entrega
	 * @return void
	 */
	public function registrar($entrega=array()) {

		//dd($entrega);

		//Entrega Info
		$this->fecha_entrega 	= !empty($entrega["fecha_entrega"]) ? $entrega["fecha_entrega"] : "";
		$this->empresa_id 		= !empty($entrega["empresa_id"]) ? $entrega["empresa_id"] : "";
		$this->entrega_id 		= !empty($entrega["entrega_id"]) ? $entrega["entrega_id"] : "";
		$this->items 					= !empty($entrega["items"]) ? $entrega["items"] : array();
		$this->calculo_costo_retorno = !empty($entrega["calculo_costo_retorno"]) ? $entrega["calculo_costo_retorno"] : "";
		$this->lista_precio_alquiler_id = !empty($entrega["lista_precio_alquiler_id"]) ? $entrega["lista_precio_alquiler_id"] : "";

		//Recorrer los items
		$count=0;
		$total_items=count($this->items);
		foreach($this->items AS $item) {

			$ciclo = !empty($item["ciclo"]) ? $item["ciclo"] : "";

			//------------------------------------------
			// Si no existe periodo, continuar
			// siguiente iteracion
			//------------------------------------------
			if(empty($this->periodo[$ciclo])){
				continue;
			}

			$ciclo_id 	= !empty($item["ciclo_id"]) ? $item["ciclo_id"] : "";
			$item_id 		= !empty($item["item_id"]) ? $item["item_id"] : "";
			$cantidad 	= !empty($item["cantidad"]) ? $item["cantidad"] : "";
			$series 		= !empty($item["series"]) ? $item["series"] : array();
			$devoluciones	= !empty($item["devoluciones"]) ? $item["devoluciones"] : array();

			unset($item["series"]);
			unset($item["devoluciones"]);

			$item["cargoable_id"] = $this->entrega_id;
			$item["cargoable_type"] = "Flexio\Modulo\EntregasAlquiler\Models\EntregasAlquiler";

			//------------------------------------------
			// Seleccionar el ultimo cargo realizado a el
			// item de esta entrega.
			//------------------------------------------
			$clause = array(
				"cargoable_id" 		=> $this->entrega_id,
				"item_id" 				=> $item_id,
				"empresa_id" 			=> $this->empresa_id,
				//"ciclo_id" 				=> $ciclo_id,
				"cargoable_type" 	=> "Flexio\Modulo\EntregasAlquiler\Models\EntregasAlquiler",
			);
			$this->ultimo_cargo = $this->ultimoCargo($clause);
			$this->fecha_ejecutar_cargo = !empty($this->ultimo_cargo) ? Carbon::parse($this->ultimo_cargo->fecha_cargo)->copy() : Carbon::parse($entrega["fecha_entrega"])->copy();

			//Fecha actual
			$this->fecha_actual = Carbon::now('America/Panama');

			//Lapso de tiempo a calcular
			$lapso_tiempo = $this->periodo[$ciclo]["lapso"];

			//------------------------------------------
			// Calcular el Tiempo Transcurrido entre
			// Fecha a ejecultar VS fecha/hora actual
			//------------------------------------------
			$tiempo_transcurrido = Carbon::parse(Carbon::parse($this->fecha_ejecutar_cargo)->copy())->{$this->periodo[$ciclo]["func_difference"]}($this->fecha_actual);

			$itemsdevueltos = array();
			//------------------------------------------
			// Verificar si Tiempo Transucrrido
			// es IGUAL a Lapso de Tiempo
			//------------------------------------------
			if($tiempo_transcurrido >= $lapso_tiempo) {

				//------------------------------------------
				// Sumarle lapso a $fecha_ejecutar_cargo
				// para obtener la fecha actual de cargo.
				//------------------------------------------
				$fecha_cargo = Carbon::parse(Carbon::parse($this->fecha_ejecutar_cargo)->copy())->{$this->periodo[$ciclo]["func_addition"]}($lapso_tiempo);

				//------------------------------------------
				// Armar array item
				// para items no serializados/serializados
				// con cantidad mayor a 1
				//------------------------------------------
				$item = $this->preparar($item, $cantidad, $series, $devoluciones, $this->empresa_id, $fecha_cargo, false, $this->calculo_costo_retorno, $this->fecha_ejecutar_cargo, $this->lista_precio_alquiler_id);

				if(empty($item)){
					continue;
				}

				//------------------------------------------
				// Guardar en DB cargo
				//------------------------------------------
				$this->guardar($this->entrega_id, $item, $item_id, $this->empresa_id);

			}else {

				$fecha_cargo = Carbon::parse(Carbon::parse($this->fecha_ejecutar_cargo)->copy())->{$this->periodo[$ciclo]["func_addition"]}($lapso_tiempo);
				$items = $this->preparar($item, $cantidad, $series, $devoluciones, $this->empresa_id, $fecha_cargo, true, $this->calculo_costo_retorno, $this->fecha_ejecutar_cargo, $this->lista_precio_alquiler_id);


				if(empty($items) || empty(Utiles::multiarray_buscar_key($items, "devuelto"))){
					continue;
				}

				//------------------------------------------
				// Guardar en DB cargos prorrateados a items devueltos
				//------------------------------------------
				$this->guardar($this->entrega_id, $items, $item_id, $this->empresa_id);
			}

			$count++;

			//------------------------------------------
			// Ejecutar Cargo Retroactivo
			//------------------------------------------
			// si el tiempo transcurrido es mayor
			// que el lapso.
			//------------------------------------------
			if($count == $total_items && $tiempo_transcurrido > $lapso_tiempo && empty($itemsdevueltos)) {
				$this->registrar($entrega);
			}
		}
	}

}
