<?php

use \Flexio\Migration\Migration;

class NotasDebitoV1 extends Migration
{
    public function up()
    {
        $this->table('compra_nota_debitos')
        ->addColumn('monto_factura', 'decimal', ['scale' => 4, 'precision' => 10, 'default' => 0])
        ->save();
    }

    public function down()
    {
        $this->table('compra_nota_debitos')
        ->removeColumn('monto_factura')
        ->save();
    }
}
