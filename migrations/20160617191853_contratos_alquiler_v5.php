<?php

use \Flexio\Migration\Migration;

class ContratosAlquilerV5 extends Migration
{
    
    public function up()
    {
        $table = $this->table('devalq_devoluciones_alquiler');
        $table
                ->addColumn('uuid_devolucion_alquiler', 'binary', array('limit' => 16))
                ->addColumn('codigo', 'string', array('limit' => 100))
                ->addColumn('fecha_devolucion', 'datetime')
                ->addColumn('estado_id', 'integer', array('limit' => 10))
                ->addColumn('referencia', 'string', array('limit' => 100))
                
                ->addColumn('created_by', 'integer', array('limit' => 10))
                ->addColumn('empresa_id', 'integer', array('limit' => 10))
                ->addColumn('created_at', 'datetime')
                ->addColumn('updated_at', 'datetime')
                ->save();
    }

    public function down()
    {
        $this->dropTable('devalq_devoluciones_alquiler');
    }
    
}
