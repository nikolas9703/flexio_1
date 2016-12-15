<?php

use \Flexio\Migration\Migration;

class ContratosAlquilerV1 extends Migration
{
    
    public function up()
    {
        $table = $this->table('conalq_contratos_alquiler');
        $table
                ->addColumn('uuid_contrato_alquiler', 'binary', array('limit' => 16))
                ->addColumn('codigo', 'string', array('limit' => 100))
                ->addColumn('cliente_id', 'integer', array('limit' => 10))
                ->addColumn('fecha_inicio', 'datetime')
                ->addColumn('saldo_facturar', 'decimal', array('scale' => 2, 'precision' => 10))
                ->addColumn('total_facturado', 'decimal', array('scale' => 2, 'precision' => 10))
                ->addColumn('estado_id', 'integer', array('limit' => 10))
                
                ->addColumn('created_by', 'integer', array('limit' => 10))
                ->addColumn('empresa_id', 'integer', array('limit' => 10))
                ->addColumn('created_at', 'datetime')
                ->addColumn('updated_at', 'datetime')
                ->save();
    }

    public function down()
    {
        $this->dropTable('conalq_contratos_alquiler');
    }
    
}
