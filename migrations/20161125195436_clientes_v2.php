<?php

use \Flexio\Migration\Migration;

class ClientesV2 extends Migration
{
    public function up()
    {
        $this->table('cli_clientes')
        ->changeColumn('exonerado_impuesto','string',['limit' => 140, 'default' => ''])
        ->save();
    }

    public function down()
    {
        $this->table('cli_clientes')
        ->changeColumn('exonerado_impuesto','decimal',['scale' => 2, 'precision' => 4, 'default' => 0])
        ->save();
    }
}
