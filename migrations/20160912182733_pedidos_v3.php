<?php

use \Flexio\Migration\Migration;

class PedidosV3 extends Migration
{

    public function up()
    {
        $table = $this->table("ped_pedidos_inv_items");

        if(!$table->hasColumn('comentario')){
            $table->addColumn('comentario', 'string', ['limit' => 140, 'default' => ''])
            ->save();
        }

    }

    public function down()
    {
        $table = $this->table("ped_pedidos_inv_items");

        if($table->hasColumn('comentario')){
            $table->removeColumn('comentario')
            ->save();
        }
    }

}
