<?php

use \Flexio\Migration\Migration;

class PagosV4 extends Migration
{

    public function up()
    {
        $this->table('pag_pagos')
        ->addColumn('empezable_type', 'string', ['limit' => 140, 'default' => ''])
        ->addColumn('empezable_id', 'integer', ['limit' => 10, 'default' => 0])
        ->save();
    }

    public function down()
    {
        $this->table('pag_pagos')
        ->removeColumn('empezable_type')
        ->removeColumn('empezable_id')
        ->save();
    }

}
