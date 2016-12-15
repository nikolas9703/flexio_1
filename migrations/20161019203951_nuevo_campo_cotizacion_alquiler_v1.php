<?php

use \Flexio\Migration\Migration;

class NuevoCampoCotizacionAlquilerV1 extends Migration
{
  public function up() {
    $table = $this->table('cotz_cotizaciones');
    $column = $table->hasColumn('lista_precio_alquiler_id');
    if (!$column) {
      $table->addColumn('lista_precio_alquiler_id', 'integer', array('limit' => 10, 'null' => true, 'after' => 'centro_facturacion_id'))->save();
    }
  }

  public function down() {
    $table = $this->table('cotz_cotizaciones');
    $column = $table->hasColumn('lista_precio_alquiler_id');
    if ($column) {
      $table->removeColumn('lista_precio_alquiler_id')->save();
    }
  }
}
