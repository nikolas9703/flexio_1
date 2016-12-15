<?php
namespace Flexio\Jobs\ContratosAlquiler\Prorrateo;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Flexio\Library\Util\Utiles;
use Flexio\Modulo\ContratosAlquiler\Models\CargosAlquiler AS CargosAlquilerModel;

class Prorrateo {

	private $metodo = NULL;
	private $periodos = array();
	public $modelo_retorno = "";

	public function __construct($modelo_retorno) {

			$this->modelo_retorno = !empty($modelo_retorno) ? trim($modelo_retorno) : NULL;

			switch ($this->modelo_retorno) {
	        case "escalonado":
							$this->metodo = new \Flexio\Jobs\ContratosAlquiler\Prorrateo\Escalonado();
							break;
					case (!preg_match("/completo|escalonado/im", $modelo_retorno) ? true : false):
	            $this->metodo = new \Flexio\Jobs\ContratosAlquiler\Prorrateo\Normal();
							break;
	        break;
	    }
	}

	public function calcular($item, $devoluciones, $periodos=array(), $fecha_cargo=NULL, $fecha_cargo_sin_lapso=NULL, $lista_precio_alquiler_id=NULL) {

		$clause = array(
			"cargoable_id" 		=> !empty($item["cargoable_id"]) ? $item["cargoable_id"] : "",
			"item_id" 				=> !empty($item["item_id"]) ? $item["item_id"] : "",
			"empresa_id" 			=> !empty($item["empresa_id"]) ? $item["empresa_id"] : "",
			"cargoable_type" 	=> "Flexio\Modulo\EntregasAlquiler\Models\EntregasAlquiler",
		);
		$fecha_cargo = Carbon::parse($fecha_cargo)->copy();
		$cantidad = !empty($item["cantidad"]) ? $item["cantidad"] : 0;
		$cantidad_devuelta = !empty($devoluciones) ? $devoluciones[0]["cantidad"] : 0;
		$cantidad_restante = ((float)$cantidad - (float)$cantidad_devuelta);
		$fecha_devolucion = !empty($devoluciones) ? $devoluciones[0]["fecha_devolucion"] : "";

		// Si el item es serializado
		// hay que hacer una busqueda de la fecha
		// de devolucion segun la serie del item.
		if(!empty($item["serie"])){

			//para verificar en la consulta la serie devuelta
			$clause["serie"] = $item["serie"];

			//informacion sobre devolucion del item
			$index = Utiles::multiarray_buscar_valor($item["serie"], "serie", $devoluciones);
			$fecha_devolucion = is_numeric($index) ? $devoluciones[$index]["fecha_devolucion"] : "";
		}

		//Verificar si el cargo ya fue ejecutado
		//para la la fecha de devolucion actual
		$clause["fecha_cargo"] = $fecha_devolucion;
		$check = CargosAlquilerModel::clauseFiltro($clause)->get();

		//verificar si cantidad devuelta existe
		if(collect($check)->toArray() ||empty($cantidad_devuelta) || $this->modelo_retorno == NULL){
			$itemsarr[] = $item;
			return $itemsarr;
		}

		//Modelo de retorno completo
		if(preg_match("/completo/im", $this->modelo_retorno)){
			$item["total_cargo"] = round(($item["tarifa"] * $cantidad), 2, PHP_ROUND_HALF_UP);
			$item["devuelto"]	= 1;
			$item["fecha_cargo"] = $fecha_devolucion;
			$item["fecha_devolucion"] = $fecha_devolucion;
			$item["cantidad"] = $cantidad_devuelta;
			$item["cantidad_devuelta"] = $cantidad_devuelta;

			$itemsarr[] = $item;
			return $itemsarr;
		}

		//verificar si cantidad devuelta existe
		if(empty($cantidad_devuelta) || $this->modelo_retorno == NULL || preg_match("/completo/im", $this->modelo_retorno)){
			$itemsarr[] = $item;
			return $itemsarr;
		}

		//Verificar antes de calcular, si la fecha de devolucion
		//es menor que la fecha de ejcucion del cargo
		//de lo contrario retornar vacio.
		if( Carbon::parse(Carbon::parse(Carbon::parse($fecha_devolucion)->copy())->format('Y-m-d'))->lt( Carbon::parse($fecha_cargo->format('Y-m-d')) )){

			//Calcular tiempo transcurrido entre la devoulucion y la fecha del cargo.
			$tiempo_transcurrido = !empty($devoluciones) ? Carbon::parse(Carbon::parse($fecha_devolucion)->copy())->{$periodos[$item["ciclo"]]["func_prorrateo"]}(Carbon::parse(Carbon::parse($item["fecha_cargo"])->format('Y-m-d H:i:s'))) : 0;

			//Precios de alquiler Filtrado segun lista de precio seleccionada
			$precios_alquiler = collect($item["precios_alquiler"])->filter(function ($value, $key) use($lista_precio_alquiler_id){
					return $value["pivot"]["id_precio"] === $lista_precio_alquiler_id;
			})->toArray();

			$preciokey = collect($precios_alquiler)->keys()->first();
			$precios_alquiler = !empty($precios_alquiler) ? $precios_alquiler[$preciokey]["pivot"] : array();

			//Armar arreglo de con valores
			$data = array(
				"fecha_devolucion" => $fecha_devolucion,
				"fecha_devolucion_formato" => $fecha_devolucion,
				"tarifa" => !empty($item["tarifa"]) ? $item["tarifa"] : 0,
				"cantidad" => $cantidad_restante,
				"cantidad_devuelta" => $cantidad_devuelta,
				"cantidad_restante" => $cantidad_restante,
				"fecha_cargo_sin_lapso" => $fecha_cargo_sin_lapso,
				"precios_alquiler" => $precios_alquiler,
				"modelo_retorno" => $this->modelo_retorno,
				"tiempo_transcurrido" => $tiempo_transcurrido,
			);

			// Cantidad a multiplicar por el total por la tarifa
			// Si el item es serializado, la cantidad debe ser igual a 1
			$cantidad = !empty($item["serie"]) ? $item["cantidad"] : $cantidad_devuelta;
			$tarifa = $this->metodo->prorratear($data);

			$item_devuelto = $item;
			$item_devuelto["tarifa"] = $tarifa;
			$item_devuelto["total_cargo"] = round(($tarifa * $cantidad), 2, PHP_ROUND_HALF_UP);
			$item_devuelto["devuelto"]	= 1;
			$item_devuelto["fecha_cargo"] = $fecha_devolucion;
			$item_devuelto["fecha_devolucion"] = $fecha_devolucion;
			$item_devuelto["cantidad"] = $cantidad_devuelta;
			$item_devuelto["cantidad_devuelta"] = $cantidad_devuelta;

			// Verificar si ya se han devuelto todos
			// los items o aun quedan.
			if($cantidad_restante > 0){
				$itm = array($item, [$item_devuelto]);
			}

			$item = [$item_devuelto];

			//Verificar si hay varios items
			if(Utiles::is_two_dimensional($item)){
				$itemsarr = $item;
			}else{
				$itemsarr[] = $item;
			}

			return $itemsarr;
		}

		//if($cantidad_restante > 0){
			$itemsarr[] = $item;
			return $itemsarr;
		//}
	}
}
