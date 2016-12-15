<?php

use \Flexio\Migration\Migration;

class InventariosV8 extends Migration
{
    protected $tableName = "inv_items";

    public function up()
    {
        $this->table($this->tableName)
        ->addColumn('cuentas', 'text', ['default'=>''])
        ->save();
    }

    public function down()
    {
        $this->table($this->tableName)
        ->removeColumn('cuentas')
        ->save();
    }
    
}
