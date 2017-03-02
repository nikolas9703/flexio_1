<?php

use \Flexio\Migration\Migration;

class ClientesV4 extends Migration
{

    public function up()
    {
        $this->table('geo_provincias')
        ->addColumn('nombre', 'string', ['limit' => 140, 'default' => ''])
        ->save();
    }

    public function down()
    {
        $this->dropTable('geo_provincias');
    }

}
