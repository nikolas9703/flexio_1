<?php
namespace Flexio\Jobs\ContratosAlquiler\CalculoRetorno;

/**
 * Interface para Metodo Escalonado/Normal
 */
interface MetodosInterface {
	public function prorratear($data=array());
	public function calcular_tarifa();
}
