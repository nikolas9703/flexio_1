<?php

use \Flexio\Migration\Migration;

class ActualizarItemsContratoAlquilerV1 extends Migration
{
  public function up() {
    $exist = $this->hasTable('contratos_items');
    if($exist) {

      $table = $this->table('contratos_items');

      $column = $table->hasColumn('item_adicional');
      if (!$column) {
        $table->addColumn('item_adicional', 'integer',array('limit' => 10, 'after' => 'impuesto_id'));
      }

      $column = $table->hasColumn('empresa_id');
      if (!$column) {
        $table->addColumn('empresa_id', 'integer',array('limit' => 10, 'after' => 'categoria_id'));
      }

      $column = $table->hasColumn('unidad_id');
      if (!$column) {
        $table->addColumn('unidad_id', 'integer',array('limit' => 10, 'after' => 'cantidad'));
      }

      $table->addIndex(array('empresa_id', 'unidad_id'))->save();
    }
  }

  public function down() {
    $exist = $this->hasTable('contratos_items');
    if($exist) {

      $table = $this->table('contratos_items');

      $column = $table->hasColumn('item_adicional');
      if ($column) {
        $table->removeColumn('item_adicional');
      }

      $column = $table->hasColumn('empresa_id');
      if ($column) {
        $table->removeColumn('empresa_id');
      }

      $column = $table->hasColumn('unidad_id');
      if ($column) {
        $table->removeColumn('unidad_id');
      }
    }
  }
}
