<?php

use \Flexio\Migration\Migration;

class SeriesV2 extends Migration
{
    public function up()
    {
        $this->table('inv_items_seriales')
        ->addColumn('empresa_id', 'integer', ['limit' => 11])
        ->addColumn('estado', 'string', ['limit' => 140, 'default' => 'disponible'])
        ->save();
    }

    public function down()
    {
        $this->table('inv_items_seriales')
        ->removeColumn('empresa_id')
        ->removeColumn('estado')
        ->save();
    }
}
