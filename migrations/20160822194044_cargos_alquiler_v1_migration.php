<?php

use \Flexio\Migration\Migration;

class CargosAlquilerV1Migration extends Migration
{
    public function up() {
      $exist = $this->hasTable('car_cargos_alquiler');
  		if($exist) {
        $this->dropTable('car_cargos_alquiler');
      }

      $table = $this->table('car_cargos_alquiler');
    	$table->addColumn('empresa_id', 'integer', array('limit' => 10))
        ->addColumn('uuid_cargo', 'binary', array('limit' => 16))
        ->addColumn('numero', 'string', ["limit" => 100])
        ->addColumn('cargoable_id', 'integer', array('limit' => 10))
	    	->addColumn('cargoable_type', 'text')
        ->addColumn('contrato_id', 'integer', array('limit' => 10))
	    	->addColumn('item_id', 'integer', array('limit' => 10))
	    	->addColumn('cantidad', 'integer', array('limit' => 10))
        ->addColumn('cantidad_devuelta', 'integer', array('limit' => 10, 'default' => 0))
        ->addColumn('serie', 'integer', array('limit' => 10))
	    	->addColumn('tarifa', 'decimal', array('scale' => 2, 'precision' => 13))
        ->addColumn('total_cargo', 'decimal', array('scale' => 2, 'precision' => 13))
        ->addColumn('ciclo_id', 'integer', array('limit' => 10))
        ->addColumn('ciclo', 'string', array('limit' => 100, 'default' => ''))
        ->addColumn('devuelto', 'boolean', ['default' => false, 'null' => true])
        ->addColumn('fecha_siguiente_cargo', 'datetime')
        ->addColumn('fecha_devolucion', 'datetime')
        ->addColumn('estado', 'enum', array('values' => ['por_facturar', 'facturado', 'anulado'], 'default' => 'por_facturar'))
	    	->addColumn('created_at', 'datetime')
	    	->addColumn('updated_at', 'datetime')
	    	->addIndex(array('cargoable_id'))
	    	->addIndex(array('item_id'))
	    	->save();
    }

    public function down() {
      $exist = $this->hasTable('car_cargos_alquiler');
  		if($exist) {
        $this->dropTable('car_cargos_alquiler');
      }
    }
}
