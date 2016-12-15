<?php

use \Flexio\Migration\Migration;

class CamposItemsAlquiler extends Migration
{
  public function up() {
    $table = $this->table('lines_items');

    //Cambiar nombre columna periodo_tarifario_id
    $column = $table->hasColumn('periodo_tarifario_id');
    if ($column) {
      $table->renameColumn('periodo_tarifario_id', 'tarifa_periodo_id');
    }

    //fecha desde
    $column = $table->hasColumn('tarifa_fecha_desde');
    if (!$column) {
      $table->addColumn('tarifa_fecha_desde', 'datetime', array('null' => true, 'after' => 'tarifa_periodo_id'));
    }

    //fecha hasta
    $column = $table->hasColumn('tarifa_fecha_hasta');
    if (!$column) {
      $table->addColumn('tarifa_fecha_hasta', 'datetime', array('null' => true, 'after' => 'tarifa_fecha_desde'));
    }

    $column = $table->hasColumn('tarifa_pactada');
    if (!$column) {
      $table->addColumn('tarifa_pactada', 'string', array('limit' => 255, 'null' => true, 'after' => 'tarifa_fecha_hasta'));
    }

    $column = $table->hasColumn('tarifa_monto');
    if (!$column) {
      $table->addColumn('tarifa_monto', 'decimal', array('scale' => 2, 'precision' => 13, 'after' => 'tarifa_pactada'));
    }

    $column = $table->hasColumn('tarifa_cantidad_periodo');
    if (!$column) {
      $table->addColumn('tarifa_cantidad_periodo', 'integer',array('limit' => 10, 'after' => 'tarifa_monto'));
    }

    $table->addIndex(array('tarifa_periodo_id'))->save();
  }

  public function down() {
    $table = $this->table('lines_items');

    $column = $table->hasColumn('tarifa_periodo_id');
    if ($column) {
      $table->renameColumn('tarifa_periodo_id', 'periodo_tarifario_id');
    }
    $column = $table->hasColumn('tarifa_fecha_desde');
    if ($column) {
      $table->removeColumn('tarifa_fecha_desde');
    }
    $column = $table->hasColumn('tarifa_fecha_hasta');
    if ($column) {
      $table->removeColumn('tarifa_fecha_hasta');
    }
    $column = $table->hasColumn('tarifa_pactada');
    if ($column) {
      $table->removeColumn('tarifa_pactada');
    }
    $column = $table->hasColumn('tarifa_monto');
    if ($column) {
      $table->removeColumn('tarifa_monto');
    }
    $column = $table->hasColumn('tarifa_cantidad_periodo');
    if ($column) {
      $table->removeColumn('tarifa_cantidad_periodo');
    }
  }
}
