<?php

use \Flexio\Migration\Migration;

class EntradaManualV1 extends Migration
{
    public function up()
    {
        $this->table('contab_entrada_manual')
        ->addColumn('fecha_entrada', 'datetime')
        ->save();
    }

    public function down()
    {
        $this->table('contab_entrada_manual')
        ->removeColumn('fecha_entrada')
        ->save();
    }
}
