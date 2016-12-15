<?php

use \Flexio\Migration\Migration;

class NuevoCampoItemInventarioV1 extends Migration
{
  public function up() {
    $table = $this->table('inv_items');
    $column = $table->hasColumn('tarifa_4_horas');
    if (!$column) {
      $table->addColumn('tarifa_4_horas', 'decimal', array('scale' => 2, 'precision' => 10, 'null' => true, 'after' => 'tarifa_mensual'))->save();
    }
  }

  public function down() {
    $table = $this->table('inv_items');
    $column = $table->hasColumn('tarifa_4_horas');
    if ($column) {
      $table->removeColumn('tarifa_4_horas')->save();
    }
  }
}
