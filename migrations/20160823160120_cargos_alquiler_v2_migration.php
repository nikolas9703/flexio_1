<?php
use \Flexio\Migration\Migration;

class CargosAlquilerV2Migration extends Migration {

	public function up() {
		$exist = $this->hasTable('car_cargos_alquiler');
		if($exist) {
			$tabla = $this->table('car_cargos_alquiler');
			$tabla->renameColumn('fecha_siguiente_cargo', 'fecha_cargo');
		}
	}

	public function down() {
		$exist = $this->hasTable('car_cargos_alquiler');
		if($exist) {
			$tabla = $this->table('car_cargos_alquiler');
	    $tabla->renameColumn('fecha_cargo', 'fecha_siguiente_cargo');
		}
	}
}
