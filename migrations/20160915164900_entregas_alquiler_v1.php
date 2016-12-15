<?php

use \Flexio\Migration\Migration;

class EntregasAlquilerV1 extends Migration
{

    public function up(){

        $this->table('contratos_items_detalles')
        ->addColumn('atributo_id', 'integer', ['limit' => 10, 'default' => 0])
        ->addColumn('atributo_text', 'string', ['limit' => 140, 'default' => ''])
        ->save();

    }

    public function down(){

        $this->table('contratos_items_detalles')
        ->removeColumn('atributo_id')
        ->removeColumn('atributo_text')
        ->save();

    }

}
