<?php
namespace Flexio\Jobs\ContratosAlquiler;

/**
 * Interface para Cargos de Alquiler Programados
 */
interface CargosProgramadosInterface {
	public function __construct();
	public function registrar($entrega=array());
}
