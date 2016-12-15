<?php
namespace Flexio\Jobs\ContratosAlquiler;

interface CortesFacturacionInterface {
	public function verificar($contrato=NULL);
	public function preparar($contrato=NULL, $fecha_orden_venta=NULL);
	public function guardar($fieldset, $items, $cargos);
}
