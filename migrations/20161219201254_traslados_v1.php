<?php

use \Flexio\Migration\Migration;

class TrasladosV1 extends Migration
{
    public function up()
    {
        $this->table('tras_traslados')
        ->addColumn('observaciones', 'text', ['default' => ''])
        ->save();
    }

    public function down()
    {
        $this->table('tras_traslados')
        ->removeColumn('observaciones')
        ->save();
    }

}
