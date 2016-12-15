<?php

use \Flexio\Migration\Migration;

class NotasDebitoV2 extends Migration
{
    public function up()
    {
        $this->table('compra_nota_debitos')
        ->addColumn('fecha_factura', 'datetime')
        ->save();
    }

    public function down()
    {
        $this->table('compra_nota_debitos')
        ->removeColumn('fecha_factura')
        ->save();
    }

}
