<?php

use \Flexio\Migration\Migration;

class ClientesV10 extends Migration
{
    public function up()
    {
        $this->table('cli_centros_facturacion')
        ->changeColumn('eliminado', 'integer', ['limit' => 11, 'default' => 0])
        ->save();
    }

    public function down()
    {
        $this->table('cli_centros_facturacion')
        ->changeColumn('eliminado', 'integer', ['limit' => 11])
        ->save();
    }
}
