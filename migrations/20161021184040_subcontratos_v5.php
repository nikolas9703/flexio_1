<?php

use \Flexio\Migration\Migration;

class SubcontratosV5 extends Migration
{

    public function up()
    {
        $this->table('sub_subcontratos')
        ->addColumn('estado', 'string', ['limit' => 140, 'default' => 'por_aprobar'])
        ->save();
    }

    public function down()
    {
        $this->table('sub_subcontratos')
        ->removeColumn('estado')
        ->save();
    }

}
