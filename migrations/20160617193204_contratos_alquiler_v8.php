<?php

use \Flexio\Migration\Migration;

class ContratosAlquilerV8 extends Migration
{
    public function up()
    {
        $table = $this->table('entregas_devoluciones');
        $table
                ->addColumn('entrega_id', 'integer', array('limit' => 11))
                ->addColumn('devolucion_id', 'string', array('limit' => 100))
                ->save();
    }

    public function down()
    {
        $this->dropTable('entregas_devoluciones');
    }
}
