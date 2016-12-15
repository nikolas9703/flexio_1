<?php

use \Flexio\Migration\Migration;

class ContratosAlquilerV6 extends Migration
{
    public function up()
    {
        $table = $this->table('devalq_devoluciones_alquiler_catalogos');
        $table
                ->addColumn('nombre', 'string', array('limit' => 100))
                ->addColumn('valor', 'string', array('limit' => 100))
                ->addColumn('tipo', 'string', array('limit' => 100))
                ->save();
    }

    public function down()
    {
        $this->dropTable('devalq_devoluciones_alquiler_catalogos');
    }
}
