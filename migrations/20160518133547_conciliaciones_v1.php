<?php

use \Flexio\Migration\Migration;

class ConciliacionesV1 extends Migration
{
    /**
     * Conciliaciones V1 solo incluye soporte para las funcionalidades
     * 1.- Tabla principal de conciliaciones
     * 2.- Búsqueda avanzada de conciliaciones
     * 
     * PD. El soporte para las funcionalidades crear y ver serán incluidas
     * en la versión 2.
     */
    
    public function up()
    {
        $table = $this->table('conc_conciliaciones');
        $table
                ->addColumn('uuid_conciliacion', 'binary', array('limit' => 16))
                ->addColumn('codigo', 'string', array('limit' => 100))
                
                ->addColumn('balance_banco', 'decimal', array('scale' => 2, 'precision' => 10))
                ->addColumn('balance_flexio', 'decimal', array('scale' => 2, 'precision' => 10))
                ->addColumn('diferencia', 'decimal', array('scale' => 2, 'precision' => 10))
                
                ->addColumn('fecha_inicio', 'datetime')
                ->addColumn('fecha_fin', 'datetime')
                
                //foreign keys
                ->addColumn('cuenta_id', 'integer', array('limit' => 10))
                
                //no functionals requeriments
                ->addColumn('empresa_id', 'integer', array('limit' => 10))
                ->addColumn('created_by', 'integer', array('limit' => 10))
                ->addColumn('updated_at', 'datetime')
                ->addColumn('created_at', 'datetime')
                
                ->save();
    }

    public function down()
    {
        $this->dropTable('conc_conciliaciones');
    }
}
