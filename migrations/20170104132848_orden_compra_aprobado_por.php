<?php

use \Flexio\Migration\Migration;
//ord_ordenes
class OrdenCompraAprobadoPor extends Migration
{
  public function up() {
    //TABLA ORDENES DE COMPRAS
    $exist = $this->hasTable('ord_ordenes');
    if($exist) {
      $table = $this->table('ord_ordenes');
      $column = $table->hasColumn('aprobado_por');
      if (!$column) {
        $table->addColumn('aprobado_por', 'integer', array('limit' => 10, 'after' => 'id_empresa'))
          ->addIndex(array('aprobado_por'))
          ->save();
      }
    }
  }

  public function down() {
    //TABLA ORDENES DE COMPRAS
    $exist = $this->hasTable('ord_ordenes');
    if($exist) {
      $table = $this->table('ord_ordenes');
      $column = $table->hasColumn('aprobado_por');
      if ($column) {
        $table->removeColumn('aprobado_por');
      }
    }
  }
}
