<?php

use \Flexio\Migration\Migration;

class NuevoCampoContratoAlquilerV2 extends Migration
{
    public function up() {
      $table = $this->table('conalq_contratos_alquiler');
      $column = $table->hasColumn('lista_precio_alquiler_id');
      if (!$column) {
        $table->addColumn('lista_precio_alquiler_id', 'integer', array('limit' => 10, 'null' => true, 'after' => 'calculo_costo_retorno_id'))->save();
      }
    }

    public function down() {
      $table = $this->table('conalq_contratos_alquiler');
      $column = $table->hasColumn('lista_precio_alquiler_id');
      if ($column) {
        $table->removeColumn('lista_precio_alquiler_id')->save();
      }
    }
}
