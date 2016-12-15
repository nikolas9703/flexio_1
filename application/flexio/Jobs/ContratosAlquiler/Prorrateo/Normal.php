<?php
namespace Flexio\Jobs\ContratosAlquiler\Prorrateo;

class Normal implements MetodosInterface {

	private $tarifa;
	private $modelo_retorno;
	private $tiempo_transcurrido;

	public function prorratear($data=array()) {

		if(empty($data)){
			continue;
		}
		extract($data);

		$this->tarifa = $tarifa;
		$this->modelo_retorno = $modelo_retorno;
		$this->tiempo_transcurrido = $tiempo_transcurrido;
		
		return $this->calcular_tarifa();
	}

	public function calcular_tarifa	(){
		$divisor_prorrateo = intval($this->modelo_retorno);
		return round((($this->tarifa/$divisor_prorrateo)*$this->tiempo_transcurrido), 2, PHP_ROUND_HALF_UP);
	}
}
