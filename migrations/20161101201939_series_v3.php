<?php

use \Flexio\Migration\Migration;

class SeriesV3 extends Migration
{
    public function up()
    {
        $this->table('inv_items_seriales')
        ->addColumn('bodega_id', 'integer', ['limit' => 11])
        ->addColumn('cliente_id', 'integer', ['limit' => 11])
        ->addColumn('centro_facturacion_id', 'integer', ['limit' => 11])
        ->save();
    }

    public function down()
    {
        $this->table('inv_items_seriales')
        ->removeColumn('bodega_id')
        ->removeColumn('cliente_id')
        ->removeColumn('centro_facturacion_id')
        ->save();
    }
}
