<?php

use \Flexio\Migration\Migration;

class NuevoCampoAlquilerV1 extends Migration
{
  public function up() {

    // CONTRATOS DE ALQUILER
    $table = $this->table('conalq_contratos_alquiler');
    $column = $table->hasColumn('fecha_fin');
    if (!$column) {
      $table->addColumn('fecha_fin', 'datetime', array('after' => 'fecha_inicio'))->save();
    }
  }

  public function down() {
    // CONTRATOS DE ALQUILER
    $table = $this->table('conalq_contratos_alquiler');
    $column = $table->hasColumn('fecha_fin');
    if ($column) {
      $table->removeColumn('fecha_fin')->save();
    }
  }
}
