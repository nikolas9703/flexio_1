<?php

use \Flexio\Migration\Migration;

class AddColumEstadoClienteMigration extends Migration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $tabla = $this->table('cli_clientes');

        $column = $tabla->hasColumn('estado');
        if (!$column) {
            $tabla->addColumn('estado', 'string', array('limit' => 100, 'null' => true, 'default' => 'por_aprobar'))->update();
        }
    }
}
