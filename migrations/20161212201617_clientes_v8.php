<?php

use \Flexio\Migration\Migration;

class ClientesV8 extends Migration
{
    public function up()
    {
        $this->table('geo_corregimientos')
        ->addColumn('nombre', 'string', ['limit' => 140, 'default' => ''])
        ->addColumn('distrito_id', 'integer', ['limit' => 10, 'default' => 0])
        ->save();
    }

    public function down()
    {
        $this->dropTable('geo_corregimientos');
    }
}
