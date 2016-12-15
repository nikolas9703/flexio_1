<?php

use \Flexio\Migration\Migration;

class UsuariosV1 extends Migration
{
    public function up()
    {
        $this->table('usuarios')
        ->addColumn('filtro_centro_contable', 'string', ['limit' => 140, 'default' => ''])
        ->save();
    }

    public function down()
    {
        $this->table('usuarios')
        ->removeColumn('filtro_centro_contable')
        ->save();
    }
}
