<?php

use \Flexio\Migration\Migration;

class UsuariosV2 extends Migration
{
    public function up()
    {
        $this->table('usuarios_has_centros')
        ->addColumn('usuario_id', 'integer', ['limit' => 10, 'default' => 0])
        ->addColumn('centro_id', 'integer', ['limit' => 10, 'default' => 0])
        ->addColumn('empresa_id', 'integer', ['limit' => 10, 'default' => 0])
        ->save();
    }

    public function down()
    {
        $this->dropTable('usuarios_has_centros');
    }
}
