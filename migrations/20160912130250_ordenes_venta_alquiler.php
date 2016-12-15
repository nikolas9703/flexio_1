<?php

use \Flexio\Migration\Migration;

class OrdenesVentaAlquiler extends Migration
{
  public function up() {
    $exist = $this->hasTable('ord_ventas_alquiler');
    if($exist) {
      $this->dropTable('ord_ventas_alquiler');
    }

    //Tabla orden de ventas de alquiler
    $table = $this->table('ord_ventas_alquiler');
    $table->addColumn('empresa_id', 'integer', array('limit' => 10))
      ->addColumn('uuid_venta', 'binary', array('limit' => 16))
      ->addColumn('codigo', 'string', ["limit" => 100])
      ->addColumn('referencia', 'string', ["limit" => 150])
      ->addColumn('centro_contable_id', 'integer', array('limit' => 10))
      ->addColumn('centro_facturacion_id', 'integer', array('limit' => 10))
      ->addColumn('bodega_id', 'integer', array('limit' => 10))
      ->addColumn('cliente_id', 'integer', array('limit' => 10))
      ->addColumn('created_by', 'integer', array('limit' => 10))
      ->addColumn('contrato_id', 'integer', array('limit' => 10))
      ->addColumn('fecha_desde', 'datetime')
      ->addColumn('fecha_hasta', 'datetime')
      ->addColumn('comentario', 'text')
      ->addColumn('termino_pago', 'string', ["limit" => 100])
      ->addColumn('fecha_termino_pago', 'datetime')
      ->addColumn('item_precio_id', 'integer', array('limit' => 10))
      ->addColumn('subtotal', 'decimal', array('scale' => 2, 'precision' => 13))
      ->addColumn('impuestos', 'decimal', array('scale' => 2, 'precision' => 13))
      ->addColumn('total', 'decimal', array('scale' => 2, 'precision' => 13))
      ->addColumn('descuento', 'decimal', array('scale' => 2, 'precision' => 13))
      ->addColumn('estado', 'string', ["limit" => 100])
      ->addColumn('formulario', 'string', ["limit" => 100])
      ->addColumn('created_at', 'datetime')
      ->addColumn('updated_at', 'datetime')
      ->addIndex(array('bodega_id'))
      ->addIndex(array('cliente_id'))
      ->addIndex(array('empresa_id'))
      ->addIndex(array('centro_contable_id'))
      ->addIndex(array('centro_facturacion_id'))
      ->save();

      //actualizar campos not null de LineItems
      $this->updateLineItems();
  }

  private function updateLineItems() {
    $table = $this->table('lines_items');

    $column = $table->hasColumn('cantidad');
    if ($column) {
      $table->changeColumn('cantidad', 'decimal', array('scale' => 4, 'precision' => 10, 'null' => true))->save();
    }

    $column = $table->hasColumn('unidad_id');
    if ($column) {
      $table->changeColumn('unidad_id', 'integer', array('limit' => 10, 'null' => true))->save();
    }

    $column = $table->hasColumn('precio_unidad');
    if ($column) {
      $table->changeColumn('precio_unidad', 'decimal', array('scale' => 4, 'precision' => 10, 'null' => true))->save();
    }

    $column = $table->hasColumn('impuesto_id');
    if ($column) {
      $table->changeColumn('impuesto_id', 'integer', array('limit' => 10, 'null' => true))->save();
    }

    $column = $table->hasColumn('cuenta_id');
    if ($column) {
      $table->changeColumn('cuenta_id', 'integer', array('limit' => 10, 'null' => true))->save();
    }

    $column = $table->hasColumn('observacion');
    if ($column) {
      $table->changeColumn('observacion', 'string', ["limit" => 200, 'null' => true])->save();
    }

    $column = $table->hasColumn('cantidad2');
    if ($column) {
      $table->changeColumn('cantidad2', 'decimal', array('scale' => 4, 'precision' => 10, 'null' => true))->save();
    }

    $column = $table->hasColumn('cantidad_devolucion');
    if ($column) {
      $table->changeColumn('cantidad_devolucion', 'decimal', array('scale' => 4, 'precision' => 10, 'null' => true))->save();
    }

    $column = $table->hasColumn('comentario');
    if ($column) {
      $table->changeColumn('comentario', 'text', array('null' => true))->save();
    }

    $column = $table->hasColumn('comentario');
    if ($column) {
      $table->changeColumn('comentario', 'text', array('null' => true))->save();
    }

    $column = $table->hasColumn('tarifa_periodo_id');
    if ($column) {
      $table->changeColumn('tarifa_periodo_id', 'integer', array('limit' => 10, 'null' => true))->save();
    }

    $column = $table->hasColumn('tarifa_monto');
    if ($column) {
      $table->changeColumn('tarifa_monto', 'decimal', array('scale' => 2, 'precision' => 13, 'null' => true))->save();
    }

    $column = $table->hasColumn('tarifa_cantidad_periodo');
    if ($column) {
      $table->changeColumn('tarifa_cantidad_periodo', 'integer', array('limit' => 10, 'null' => true))->save();
    }
  }

  public function down() {
    $exist = $this->hasTable('ord_ventas_alquiler');
    if($exist) {
      $this->dropTable('car_cargos_alquiler');
    }
  }
}
