<?php

use \Flexio\Migration\Migration;

class ConciliacionesV4 extends Migration
{
    public function up()
    {
        $table = $this->table('contab_transacciones');
        $table
                ->changeColumn('balance_verificado', 'decimal', array('scale' => 2, 'precision' => 10, 'default' => 0))
                
                ->changeColumn('conciliacion_id', 'integer', array('limit' => 10, 'default' => 0))
                ->changeColumn('order', 'integer', array('limit' => 10, 'default' => 0))
                
                ->save();
    }

    public function down()
    {
        //...
    }
}
