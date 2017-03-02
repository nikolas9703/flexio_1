<?php

use \Flexio\Migration\Migration;

class AlterCargoMigrationV1 extends Migration
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
         $users = $this->table('pln_config_recargos');
         $users->changeColumn('porcentaje_hora', 'decimal', array('scale' => 2, 'precision' => 10))
               ->save();
     }

}
