<?php

use \Flexio\Migration\Migration;

class AddPlanillaIdMigration2 extends Migration
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
     public function up() {
      $table = $this->table('contab_transacciones');
      $column = $table->hasColumn('colaborador_id');
      if (!$column) {
        $table->addColumn('colaborador_id', 'integer', array('after' => 'conciliacion_id', 'null' => true))->save();
      }
    }
}
