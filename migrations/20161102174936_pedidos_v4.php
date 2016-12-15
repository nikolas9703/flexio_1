<?php

use \Flexio\Migration\Migration;

class PedidosV4 extends Migration
{
    public function up()
    {
        $this->table('ped_pedidos_inv_items')
        ->changeColumn('cantidad', 'decimal', ['scale' => 2, 'precision' => 10, 'default' => 0])
        ->save();
    }

    public function down()
    {
        $this->table('ped_pedidos_inv_items')
        ->changeColumn('cantidad', 'integer', ['limit' => 11, 'default' => 0])
        ->save();
    }
}
