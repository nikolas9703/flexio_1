<?php

use \Flexio\Migration\Migration;

class NuevoCampoAlquilerV2 extends Migration
{
  public function up() {

    $table = $this->table('devalq_devoluciones_alquiler');

    //Eliminar campo fecha_alquiler_contrato
    $table->removeColumn('fecha_alquiler_contrato');

    //AGREGAR NUEVO CAMPO FECHA INICIO CONTRATO
    $column = $table->hasColumn('fecha_inicio_contrato');
    if (!$column) {
      $table->addColumn('fecha_inicio_contrato', 'datetime', array('after' => 'fecha_devolucion'))->save();
    }

    //AGREGAR NUEVO CAMPO FECHA FIN CONTRATO
    $column = $table->hasColumn('fecha_fin_contrato');
    if (!$column) {
      $table->addColumn('fecha_fin_contrato', 'datetime', array('after' => 'fecha_inicio_contrato'))->save();
    }
  }

  public function down() {

    $table = $this->table('conalq_contratos_alquiler');

    //agregar columna nuevamente
    $table->addColumn('fecha_alquiler_contrato','datetime', array('after' => 'updated_at '));

    //AGREGAR NUEVO CAMPO FECHA INICIO CONTRATO
    $column = $table->hasColumn('fecha_inicio_contrato');
    if ($column) {
      $table->removeColumn('fecha_inicio_contrato')->save();
    }

    //AGREGAR NUEVO CAMPO FECHA FIN CONTRATO
    $column = $table->hasColumn('fecha_fin_contrato');
    if ($column) {
      $table->removeColumn('fecha_fin_contrato')->save();
    }
  }
}
