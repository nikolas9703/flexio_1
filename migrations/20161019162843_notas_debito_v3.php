<?php

use \Flexio\Migration\Migration;

class NotasDebitoV3 extends Migration
{

    public function up()
    {
        $this->table('compra_nota_debito_items')
        ->addColumn('precio_total', 'decimal', ['scale' => 4, 'precision' => 10, 'default' => 0])
        ->save();
    }

    public function down()
    {
        $this->table('compra_nota_debito_items')
        ->removeColumn('precio_total')
        ->save();
    }

}
