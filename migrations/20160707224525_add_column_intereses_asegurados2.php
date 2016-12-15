<?php

use \Flexio\Migration\Migration;

class AddColumnInteresesAsegurados2 extends Migration
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
        $tabla = $this->table('int_intereses_asegurados');
        $tabla->addColumn('uuid_intereses', 'binary', array('limit' => 16))
                ->addColumn('empresa_id', 'integer', array('limit' => 10))
                ->addColumn('interesestable_type', 'string', array('limit' => 100))
                ->addColumn('interesestable_id', 'integer', array('limit' => 10))
                ->addColumn('updated_at', 'datetime')
                ->addColumn('created_at', 'datetime')
              ->save();
    }
}
