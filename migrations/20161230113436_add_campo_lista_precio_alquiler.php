<?php

use \Flexio\Migration\Migration;

class AddCampoListaPrecioAlquiler extends Migration
{
  public function up() {
    //TABLA ORDENES DE ALQUILER
    $exist = $this->hasTable('ord_ventas_alquiler');
    if($exist) {
      $table = $this->table('ord_ventas_alquiler');
      $column = $table->hasColumn('lista_precio_alquiler_id');
      if (!$column) {
        $table->addColumn('lista_precio_alquiler_id', 'integer', array('limit' => 10, 'after' => 'item_precio_id'))
          ->addIndex(array('lista_precio_alquiler_id'))
          ->save();
      }
    }

    //TABLA FACTURA DE VENTAS
    $exist = $this->hasTable('fac_facturas');
    if($exist) {
      $table = $this->table('fac_facturas');
      $column = $table->hasColumn('lista_precio_alquiler_id');
      if (!$column) {
        $table->addColumn('lista_precio_alquiler_id', 'integer', array('limit' => 10, 'after' => 'item_precio_id'))
          ->addIndex(array('lista_precio_alquiler_id'))
          ->save();
      }
    }
  }

  public function down() {
    //TABLA ORDENES DE ALQUILER
    $exist = $this->hasTable('ord_ventas_alquiler');
    if($exist) {
      $table = $this->table('ord_ventas_alquiler');
      $table->removeColumn('lista_precio_alquiler_id');
    }

    //TABLA FACTURA DE VENTAS
    $exist = $this->hasTable('fac_facturas');
    if($exist) {
      $table = $this->table('fac_facturas');
      $table->removeColumn('lista_precio_alquiler_id');
    }
  }
}
