<?php

use \Flexio\Migration\Migration;

class NuevoCampoItemInventarioV2 extends Migration
{
  public function up() {
    $table = $this->table('inv_items');

    //15 DIAS
    $column = $table->hasColumn('tarifa_15_dias');
    if (!$column) {
      $table->addColumn('tarifa_15_dias', 'decimal', array('scale' => 2, 'precision' => 10, 'null' => true, 'after' => 'tarifa_4_horas'))->save();
    }

    //28 DIAS
    $column = $table->hasColumn('tarifa_28_dias');
    if (!$column) {
      $table->addColumn('tarifa_28_dias', 'decimal', array('scale' => 2, 'precision' => 10, 'null' => true, 'after' => 'tarifa_15_dias'))->save();
    }

    //30 DIAS
    $column = $table->hasColumn('tarifa_30_dias');
    if (!$column) {
      $table->addColumn('tarifa_30_dias', 'decimal', array('scale' => 2, 'precision' => 10, 'null' => true, 'after' => 'tarifa_28_dias'))->save();
    }
  }

  public function down() {
    $table = $this->table('inv_items');

    //15 DIAS
    $column = $table->hasColumn('tarifa_15_dias');
    if ($column) {
      $table->removeColumn('tarifa_15_dias')->save();
    }

    //28 DIAS
    $column = $table->hasColumn('tarifa_28_dias');
    if ($column) {
      $table->removeColumn('tarifa_28_dias')->save();
    }

    //30 DIAS
    $column = $table->hasColumn('tarifa_30_dias');
    if ($column) {
      $table->removeColumn('tarifa_30_dias')->save();
    }
  }
}
