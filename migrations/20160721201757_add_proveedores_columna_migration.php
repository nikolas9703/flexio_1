<?php

use \Flexio\Migration\Migration;

class AddProveedoresColumnaMigration extends Migration
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
    public function change() {
        $tabla = $this->table('pro_proveedores');
        $tabla->addColumn('provincia', 'string', array('limit' => 255, 'null' => NULL));
        $tabla->addColumn('letra', 'string', array('limit' => 255, 'null' => NULL));
        $tabla->addColumn('pasaporte', 'string', array('limit' => 255, 'null' => NULL))
              ->save();
    }
}