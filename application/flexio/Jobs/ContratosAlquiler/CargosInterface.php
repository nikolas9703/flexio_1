<?php
namespace Flexio\Jobs\ContratosAlquiler;

/**
 * Interface para Cargos de Alquiler Programados
 */
interface CargosInterface {
	public function guardar();
	public function ultimoCargo();
	public function preparar($item, $cantidad, $series, $devoluciones, $empresa_id, $fecha_cargo, $devolucion_info=false, $calculo_costo_retorno=NULL);
}
