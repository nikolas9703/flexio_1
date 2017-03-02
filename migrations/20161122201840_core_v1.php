<?php

use \Flexio\Migration\Migration;

class CoreV1 extends Migration
{
    public function up()
    {
        $this->table('empresas')
        ->addColumn('modules_hidden', 'text', ['default' => ''])
        ->save();
    }

    public function down()
    {
        $this->table('empresas')
        ->removeColumn('modules_hidden')
        ->save();
    }

}
