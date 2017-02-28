<?php
namespace Flexio\Jobs\ContratosAlquiler\CalculoRetorno;

use Carbon\Carbon;
use Carbon\CarbonInterval;

class Prorrateo implements MetodosInterface {

	private $tarifa;
	private $modelo_retorno;
	public $fecha_cargo = "";
	public $fecha_devolucion = "";
	public $dias_transcurridos = 0;

	public function prorratear($data=array()) {

		if(empty($data)){
			continue;
		}
		extract($data);

		$this->tarifa = $tarifa;

		$this->modelo_retorno = $modelo_retorno;

		/**
		 * Se agrega un dia mas a la fecha de devolucion
		 * para contar que el dia de la decolucion cuente
		 * para generar un dia mas de cargo al alquiler.
		 */

		$this->fecha_devolucion = Carbon::parse(Carbon::parse($fecha_devolucion)->copy())->addDay();
		//echo "F. DEVOLUCION: ". $this->fecha_devolucion ."<br>";
		$this->fecha_cargo = $fecha_cargo_sin_lapso;

		return $this->calcular_tarifa();
	}

	public function dias_transcurridos(){
		return Carbon::parse($this->fecha_cargo)->diffInDays(Carbon::parse(Carbon::parse($this->fecha_devolucion)->copy()));
	}

	public function calcular_tarifa	(){
		$divisor_prorrateo = intval($this->modelo_retorno);
		//echo "PRORRATEO: (($this->tarifa/$divisor_prorrateo)* ". $this->dias_transcurridos() ." )<br>";
		//echo "T. TRASNCURRIDO: ". $this->dias_transcurridos() ."<br>";
		return round((($this->tarifa/$divisor_prorrateo)*$this->dias_transcurridos()), 2, PHP_ROUND_HALF_UP);
	}
}
