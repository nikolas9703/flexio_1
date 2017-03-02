<?php

use \Flexio\Migration\Migration;

class EntregasAlquilerV2 extends Migration
{
    public function up()
    {
        $this->table('conalq_items')
        ->changeColumn('serie', 'string', ['limit' => 140, 'default' => ''])
        ->save();
    }

    public function down()
    {
        $this->table('conalq_items')
        ->changeColumn('serie', 'integer', ['limit' => 10, 'default' => '0'])
        ->save();
    }
}
