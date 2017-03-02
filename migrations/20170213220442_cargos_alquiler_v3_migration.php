<?php

use \Flexio\Migration\Migration;

class CargosAlquilerV3Migration extends Migration
{
  public function up() {
    $exist = $this->hasTable('car_cargos_alquiler');
    if($exist) {
      $tabla = $this->table('car_cargos_alquiler');
      $tabla->changeColumn('serie', 'string', array('limit' => 100, 'null' => true))->save();
    }
  }

  public function down() {
    $exist = $this->hasTable('car_cargos_alquiler');
    if($exist) {
      $tabla = $this->table('car_cargos_alquiler');
      $tabla->changeColumn('serie', 'integer', array('limit' => 10))->save();
    }
  }
}
