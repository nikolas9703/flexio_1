<?php

use \Flexio\Migration\Migration;

class NuevoCampoContratoAlquilerV1 extends Migration
{
    public function up() {
      $table = $this->table('conalq_contratos_alquiler');
      $column = $table->hasColumn('calculo_costo_retorno_id');
      if (!$column) {
        $table->addColumn('calculo_costo_retorno_id', 'integer', array('limit' => 10, 'null' => true, 'after' => 'facturar_contra_entrega_id'))->save();
      }
    }

    public function down() {
      $table = $this->table('conalq_contratos_alquiler');
      $column = $table->hasColumn('calculo_costo_retorno_id');
      if ($column) {
        $table->removeColumn('calculo_costo_retorno_id')->save();
      }
    }
}
