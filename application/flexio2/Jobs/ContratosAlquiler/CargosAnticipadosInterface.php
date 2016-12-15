<?php
namespace Flexio\Jobs\ContratosAlquiler;

/**
 * Interface para Cargos de Alquiler Anticipados
 */
interface CargosAnticipadosInterface {
	public function __construct();
	public function calcular($entrega);
	public function registrar($item=array());
}
