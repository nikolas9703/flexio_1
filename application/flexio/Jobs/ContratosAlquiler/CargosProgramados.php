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

		//Entrega Info
		$this->fecha_entrega 	= !empty($entrega["fecha_entrega"]) ? $entrega["fecha_entrega"] : "";
		$this->empresa_id 		= !empty($entrega["empresa_id"]) ? $entrega["empresa_id"] : "";
		$this->entrega_id 		= !empty($entrega["entrega_id"]) ? $entrega["entrega_id"] : "";
		$this->items 					= !empty($entrega["items"]) ? $entrega["items"] : array();
		$this->calculo_costo_retorno = !empty($entrega["calculo_costo_retorno"]) ? $entrega["calculo_costo_retorno"] : "";
		$this->lista_precio_alquiler_id = !empty($entrega["lista_precio_alquiler_id"]) ? $entrega["lista_precio_alquiler_id"] : "";

		/*if($this->entrega_id != 1){
			echo $this->entrega_id ;
			dd($entrega);
		}*/

		//Recorrer los items
		$count=0;
		$total_items=count($this->items);
		foreach($this->items AS $item) {

			$ciclo = !empty($item["ciclo"]) ? str_replace("tarifa_","",$item["ciclo"]) : "";

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
			$item["fecha_entrega"] = $this->fecha_entrega;

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

			//------------------------------------------------------
			// Si ciclo es distinto de hora
			// y no existe ultimo cargo
			// formatear la fecha para el 1er cargos a las 0:00
			//------------------------------------------------------
			if(!preg_match("/horas/i", $ciclo) && empty($this->ultimo_cargo) && empty(collect($this->ultimo_cargo)->toArray())){
				$this->fecha_ejecutar_cargo 	= Carbon::parse(Carbon::parse(Carbon::parse($this->fecha_ejecutar_cargo)->copy())->format('Y-m-d 00:00'));
			}

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
			//echo "F. CARGO: ". $this->fecha_ejecutar_cargo ."<br>";
			//echo "F. ACTUAL: ". $this->fecha_actual ."<br>";
			//echo "T. TRANSC: ". $tiempo_transcurrido ."<br>";

			//------------------------------------------
			// Verificar si Tiempo Transucrrido
			// es IGUAL a Lapso de Tiempo
			//------------------------------------------
			if($tiempo_transcurrido >= $lapso_tiempo
			&& Carbon::parse(Carbon::parse(Carbon::parse($this->fecha_ejecutar_cargo)->copy())->format('Y-m-d'))->timestamp <= Carbon::parse(Carbon::parse(Carbon::parse($this->fecha_actual)->copy())->format('Y-m-d'))->timestamp
			) {
				//echo "AQUI?";
				/*echo "FECHA ACTUAL: ". $this->fecha_actual ." <br>";
				echo "FECHA CARGO: ". $this->fecha_ejecutar_cargo ." <br>";
				echo "CICLO: ". $ciclo ." <br>";
				echo "LAPSO: ". $lapso_tiempo ." <br>";
				echo "FUNCION: ". $this->periodo[$ciclo]["func_difference"] ." <br>";
				echo "$tiempo_transcurrido >= $lapso_tiempo<br>";
				echo Carbon::parse(Carbon::parse(Carbon::parse($this->fecha_ejecutar_cargo)->copy())->format('Y-m-d'))->timestamp." <= ".Carbon::parse(Carbon::parse(Carbon::parse($this->fecha_actual)->copy())->format('Y-m-d'))->timestamp ."<br>";
				*/

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

				if(empty(array_filter($item)) || !empty($item[0]["fecha_cargo"]) && $item[0]["fecha_cargo"]=="0000-00-00 00:00:00"){
					continue;
				}

				//------------------------------------------
				// Guardar en DB cargo
				//------------------------------------------
				$this->guardar($this->entrega_id, $item, $item_id, $this->empresa_id);

			}else {

				$fecha_cargo = Carbon::parse(Carbon::parse($this->fecha_ejecutar_cargo)->copy())->{$this->periodo[$ciclo]["func_addition"]}($lapso_tiempo);
				$item["esdevolucion"] = true; //para verificar que se trata de una devolucion
				$items = $this->preparar($item, $cantidad, $series, $devoluciones, $this->empresa_id, $fecha_cargo, true, $this->calculo_costo_retorno, $this->fecha_ejecutar_cargo, $this->lista_precio_alquiler_id);

				//verificar si el cargo para este item tiene tarifa $0.00
				$hasTarifaZero = collect($items)->filter(function ($item) {
						return !empty($item["tarifa"]) && (int)$item["tarifa"] === 0;
				})->toArray();

				if(empty($items) || !empty($hasTarifaZero) || empty(Utiles::multiarray_buscar_key($items, "devuelto")) || !empty($items[0]["fecha_cargo"]) && $items[0]["fecha_cargo"]=="0000-00-00 00:00:00"){
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
