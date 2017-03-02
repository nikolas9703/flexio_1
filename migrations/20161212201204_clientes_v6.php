<?php

use \Flexio\Migration\Migration;

class ClientesV6 extends Migration
{
    public function up()
    {
        $this->table('geo_distritos')
        ->addColumn('nombre', 'string', ['limit' => 140, 'default' => ''])
        ->addColumn('provincia_id', 'integer', ['limit' => 10, 'default' => 0])
        ->save();
    }

    public function down()
    {
        $this->dropTable('geo_distritos');
    }
}
