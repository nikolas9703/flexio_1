<?php

use \Flexio\Migration\Migration;

class ConciliacionesV3 extends Migration
{
    public function up()
    {
        $table = $this->table('contab_transacciones');
        $table
                ->addColumn('balance_verificado', 'decimal', array('scale' => 2, 'precision' => 10))
                
                ->addColumn('conciliacion_id', 'integer', array('limit' => 10))
                ->addColumn('order', 'integer', array('limit' => 10))
                
                ->save();
    }

    public function down()
    {
        $table = $this->table('contab_transacciones');
        $table
                ->removeColumn('balance_verificado')
                
                ->removeColumn('conciliacion_id')
                ->removeColumn('order')
                
                ->save();
    }
}
