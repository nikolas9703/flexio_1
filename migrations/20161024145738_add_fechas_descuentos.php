<?php

use \Flexio\Migration\Migration;

class AddFechasDescuentos extends Migration
{
    private function _table_descuentos()
    {
        $table = $this->table('pln_pagadas_descuentos');
        $table
                ->addColumn('created_at', 'datetime')
                ->addColumn('updated_at', 'datetime')
                ->save();
    }
    public function up()
    {
        $this->_table_descuentos();
    }
}
