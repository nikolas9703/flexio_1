<?php
namespace Flexio\Jobs\ContratosAlquiler;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Flexio\Library\Util\Utiles;
use Flexio\Jobs\ContratosAlquiler\CargosInterface;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\ContratosAlquiler\Models\CargosAlquiler;

class Cargos implements CargosInterface{

	public $periodo = array(
		"por_hora" => array(
			"lapso" => 1,
			"func_difference" => "diffInHours",
			"func_addition" => "addHours",
			"func_prorrateo" => "diffInMinutes",
		),
		"4_horas" => array(
			"lapso" => 4,
			"func_difference" => "diffInHours",
			"func_addition" => "addHours",
			"func_prorrateo" => "diffInHours",
		),
    "6_dias" => array(
			"lapso" => 6,
			"func_difference" => "diffInDays",
			"func_addition" => "addDays",
			"func_prorrateo" => "diffInDays",
		),
		"15_dias" => array(
			"lapso" => 15,
			"func_difference" => "diffInDays",
			"func_addition" => "addDays",
			"func_prorrateo" => "diffInDays",
		),
		"28_dias" => array(
			"lapso" => 28,
			"func_difference" => "diffInDays",
			"func_addition" => "addDays",
			"func_prorrateo" => "diffInDays",
		),
		"30_dias" => array(
			"lapso" => 30,
			"func_difference" => "diffInDays",
			"func_addition" => "addDays",
			"func_prorrateo" => "diffInDays",
		),
		"diario" => array(
			"lapso" => 1,
			"func_difference" => "diffInDays",
			"func_addition" => "addDays",
			"func_prorrateo" => "diffInHours",
		),
		"semanal" => array(
			"lapso" => 1,
			"func_difference" => "diffInWeeks",
			"func_addition" => "addWeeks",
			"func_prorrateo" => "diffInDays",
			"divisor_prorrateo" => 7
		),
		"mensual" => array(
			"lapso" => 1,
			"func_difference" => "diffInMonths",
			"func_addition" => "addMonths",
			"func_prorrateo" => "diffInDays",
			"divisor_prorrateo" => 30
		)
	);

	public $periodo_facturacion = array(
		"diario" => array(
			"lapso" => 1,
			"func_difference" => "diffInDays",
			"func_addition" => "addDays",
		),
		"15_dias" => array(
			"lapso" => 15,
			"func_difference" => "diffInDays",
			"func_addition" => "addDays",
		),
		"28_dias" => array(
			"lapso" => 28,
			"func_difference" => "diffInDays",
			"func_addition" => "addDays",
		),
		"30_dias" => array(
			"lapso" => 30,
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

	/**
 	 * Arma el arreglo de cargos a registra en la DB.
 	 *
 	 * Se duplica el arreglo $item segun la cantidad.
 	 * Si existe serie, se le adjunta serie a cada item.
 	 * Si existe devoluciones se verifica el item serializado devuelto.
 	 *
	 * @param  [type]  $item                  [description]
	 * @param  [type]  $cantidad              [description]
	 * @param  [type]  $series                [description]
	 * @param  [type]  $devoluciones          [description]
	 * @param  [type]  $empresa_id            [description]
	 * @param  [type]  $fecha_cargo           Fecha en la que se debe ejecura el cargo.
	 * @param  boolean $devolucion_info       [description]
	 * @param  [type]  $calculo_costo_retorno [description]
	 * @param  [type]  $fecha_cargo_sin_lapso Esta es la fecha de cargo sin sumarle el cliclo en que se debe ejecutar.
	 * @return [type]                         [description]
	 */
	public function preparar($item, $cantidad, $series, $devoluciones, $empresa_id, $fecha_cargo, $devolucion_info=false, $modelo_retorno=NULL, $fecha_cargo_sin_lapso=NULL, $lista_precio_alquiler_id=NULL) {

		if(empty($cantidad) || $cantidad==0){
			return array();
		}

		$item["empresa_id"] = $empresa_id;
		$item["fecha_cargo"] = $fecha_cargo;

		if($cantidad <= 0) {
			//Verificar si hay varios items
			if(Utiles::is_two_dimensional($item)){
				$itemsarr = $item;
			}else{
				$itemsarr[] = $item;
			}
			return $itemsarr;
		}

		// ITEMS SERIALIZADOS
		$itemsarr = array();
		$series = array_unique($series);
		if(!empty($series) && count($series) > 0){

			$item["cantidad"] = 1;
			$item["total_cargo"] = $item["tarifa"];

			for ($j=0; $j<count($series); $j++){

				$serie = !empty($series[$j]) ? $series[$j] : 0;
				$item["serie"] = $serie;

				$CalculoRetorno = new \Flexio\Jobs\ContratosAlquiler\CalculoRetorno\CalculoRetorno($modelo_retorno);
				$item_info = $CalculoRetorno->calcular($item, $devoluciones, $this->periodo, $fecha_cargo, $fecha_cargo_sin_lapso, $lista_precio_alquiler_id);

				$itemsarr[] = !empty($item_info[0]) ? $item_info[0] : $item_info;
			}
			/*echo "HERE WE AER";
			echo "<pre>";
			print_r($item);
			print_r($series);
			echo "</pre>";
			dd($itemsarr);*/
			$item = $itemsarr;

		}else{

				$item["total_cargo"] = round(($item["tarifa"] * $cantidad), 2, PHP_ROUND_HALF_UP);
				$CalculoRetorno = new \Flexio\Jobs\ContratosAlquiler\CalculoRetorno\CalculoRetorno($modelo_retorno);
				$item = $CalculoRetorno->calcular($item, $devoluciones, $this->periodo, $fecha_cargo, $fecha_cargo_sin_lapso, $lista_precio_alquiler_id);
				return $item;
		}

		//Verificar si hay varios items
		if(Utiles::is_two_dimensional($item)){
			$itemsarr = $item;
		}else{
			$itemsarr[] = $item;
		}

		return $itemsarr;
	}

	/**
	 * Consultar el ultimo
	 * cargo realizado.
	 */
	public function ultimoCargo($clause=NULL){
		if($clause==NULL){
			return;
		}
		return CargosAlquiler::clauseFiltro($clause)->get()->last();
	}

	/**
	 * Guardar el registro de cargo de cada item
	 *
	 * @param  integer $entrega_id [description]
	 * @param  array   $item       [description]
	 * @return void
	 */
	public function guardar($entrega_id=0, $item=array(), $item_id=0, $empresa_id=0) {

			//if(empty($item)){
			if(empty($item) && !is_array($item)){
				return false;
			}

			$year = Carbon::now()->format('y');

			foreach ($item AS $itm){

				if(!is_array($itm)
				|| empty($itm)
				|| isset($itm['id'])
				|| !empty($itm['tarifa']) && (int)$itm['tarifa']==0
				|| empty($itm['ciclo_id'])
				|| empty($itm['cantidad'])){
					continue;
				}

				//Verificar si el cargo ya fue ejcutado
				$check = CargosAlquiler::clauseFiltro($itm)->first();

				//Verificar si ya fue devuelto
				$clause_devuleto = $itm;
				$clause_devuleto["devuelto"] = 1;
				unset($clause_devuleto["fecha_cargo"]);
				unset($clause_devuleto["fecha_devolucion"]);
				$check_devuelto = CargosAlquiler::clauseFiltro($clause_devuleto)->first();
				/*echo "HERE SI SI<pre>";
				print_r($itm);
				echo "</pre>";*/
				//continuar siguiente si ya existe o fue devuelto
				if(!empty($check) && !empty($check->toArray())
				|| !empty($check_devuelto) && !empty($check_devuelto->toArray())){
					continue;
				}

				//NUMERO DEL CARGO
				$total = CargosAlquiler::where('empresa_id', $empresa_id)->count();
				$itm["numero"] = Utiles::generar_codigo('CAR' . $year, $total + 1, 19);

				Capsule::beginTransaction();
				try {
					CargosAlquiler::create($itm);
				} catch (ValidationException $e) {
					// Rollback
					Capsule::rollback();
				}
				Capsule::commit();
				
			}

	}
}
