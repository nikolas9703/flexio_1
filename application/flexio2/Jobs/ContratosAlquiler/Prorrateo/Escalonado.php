<?php
namespace Flexio\Jobs\ContratosAlquiler\Prorrateo;

use Carbon\Carbon;
use Carbon\CarbonInterval;

class Escalonado implements MetodosInterface {

	public $data = array();
	public $tarifa = 0;
	public $precios_alquiler = array();
	public $fecha_cargo = "";
	public $fecha_devolucion = "";
	public $dias_transcurridos = 0;
	public $semanas_transcurridas = 0;
	public $tarifas = array(
		30 	=> array(
			"campo" => "tarifa_30_dias",
			"func_addition" => "addDays",
		),
		28 	=> array(
			"campo" => "tarifa_28_dias",
			"func_addition" => "addDays",
		),
		15 	=> array(
			"campo" => "tarifa_15_dias",
			"func_addition" => "addDays",
		),
		"semanal" => array(
			"campo" => "semanal",
			"func_addition" => "addWeeks",
		),
		6   => array(
			"campo" => "tarifa_6_dias",
			"func_addition" => "addDays",
		),
		"diario" => array(
			"campo" => "diario",
			"func_addition" => "addDays",
		),
	);

	public function prorratear($data=array()) {

		$this->data = $data;
		if(empty($this->data)){
			continue;
		}
		extract($this->data);

		if(empty($precios_alquiler)){
			//si listado de precio de alquiler
			//no existe, retornar la tarifa
			//fija que tenga.
			return $tarifa;
		}

		$this->fecha_cargo = $fecha_cargo_sin_lapso;
		$this->fecha_devolucion = $fecha_devolucion;
		$this->precios_alquiler = $precios_alquiler;

		//Calcular Tarifa Escalonada
		$this->calcular_tarifa();

		//Return
		return round($this->tarifa, 2, PHP_ROUND_HALF_UP);
	}

	public function calcular_tarifa(){

		echo "DIAS TRANSCURRIDOS: ". $this->dias_transcurridos() ."<br>";
		foreach($this->tarifas AS $periodo => $tarifa) {

			if(is_int($periodo) && (int)($this->dias_transcurridos()/intval($periodo))
			|| is_string($periodo) && preg_match("/semanal/im", $periodo) && $this->semanas_transcurridas()
			|| is_string($periodo) && preg_match("/diario/im", $periodo) && $this->dias_transcurridos()
			){
				//$calculo_periodo = is_int($periodo) && (int)($this->dias_transcurridos()/intval($periodo)) ? (int)($this->dias_transcurridos()/intval($periodo)) : (is_string($periodo) && preg_match("/semanal/im", $periodo) && $this->semanas_transcurridas() ? $this->semanas_transcurridas() : is_string($periodo) && preg_match("/diario/im", $periodo) && $this->dias_transcurridos() ? $this->dias_transcurridos() : "");

				if(is_int($periodo) && (int)($this->dias_transcurridos()/intval($periodo))){
					echo "PERIODO A: $periodo<br>";
					$calculo_periodo = (int)($this->dias_transcurridos()/intval($periodo));
				}

				else if(is_string($periodo) && preg_match("/semanal/im", $periodo) && $this->semanas_transcurridas()){
					echo "PERIODO B: $periodo<br>";
					$calculo_periodo = $this->semanas_transcurridas();
				}

				else if(is_string($periodo) && preg_match("/diario/im", $periodo) && $this->dias_transcurridos()){
					echo "PERIODO C: $periodo<br>";
					$calculo_periodo = $this->dias_transcurridos();
				}

				//Si para periodo actual no hay tarifa configurada. Continuar.
				if(empty($this->precios_alquiler[$tarifa["campo"]]) || intval($this->precios_alquiler[$tarifa["campo"]])===0){
					echo "<br><br>";
					continue;
				}

				//Actualizar monto tarifa
				$this->tarifa += $this->precios_alquiler[$tarifa["campo"]] * $calculo_periodo;

				//Obtener cantidad dias segun periodo

				//$cantidad_dias = is_numeric($periodo) ? (((int)$this->dias_transcurridos())-((int)($this->dias_transcurridos()/intval($periodo)))) : (preg_match("/semanal/im", $periodo) && $this->semanas_transcurridas()*7 ? $this->semanas_transcurridas() : preg_match("/diario/im", $periodo) && $this->dias_transcurridos() ? $this->dias_transcurridos() : "");
				if(is_numeric($periodo)){
					$cantidad_dias = $this->dias_transcurridos() < 6 ? (((int)$this->dias_transcurridos())-((int)($this->dias_transcurridos()/intval($periodo)))) : intval($periodo);
				}
				else if((preg_match("/semanal/im", $periodo) && $this->semanas_transcurridas()*7)){
					$cantidad_dias = $this->semanas_transcurridas();
				}
				else if(preg_match("/diario/im", $periodo) && $this->dias_transcurridos()){
					$cantidad_dias = $this->dias_transcurridos();
				}


				echo "FECHA: ". $this->fecha_cargo ." <br>";
				echo "AQUI ----> CANTIDAD DIAS: ". $cantidad_dias ." <br>";
				echo "FORMULA: ". $this->precios_alquiler[$tarifa["campo"]] ."  x ". $calculo_periodo ." <br><br>";
				//echo "DIAS TRANSCURRIDOS: ". (((int)$this->dias_transcurridos())-((int)($this->dias_transcurridos()/intval($periodo)))) ."<br><br>";

				//Actualizar Fecha de cargo
				$this->fecha_cargo = Carbon::parse(Carbon::parse($this->fecha_cargo)->copy())->{$tarifa["func_addition"]}($cantidad_dias);
			}
		}
	}

	public function dias_transcurridos(){
		return Carbon::parse($this->fecha_cargo)->diffInDays(Carbon::parse(Carbon::parse($this->fecha_devolucion)->copy()));
	}

	public function semanas_transcurridas(){
		return Carbon::parse($this->fecha_cargo)->diffInWeeks(Carbon::parse(Carbon::parse($this->fecha_devolucion)->copy()));
	}
}
