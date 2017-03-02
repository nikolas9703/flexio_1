<?php

use \Flexio\Migration\Migration;

class AddColumnColaboradoresv1 extends Migration
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
       $table = $this->table('col_colaboradores');
       $column = $table->hasColumn('digito_verificador');
       if (!$column) {
         $table->addColumn('digito_verificador', 'string', array('limit' => 255, 'null' => true))->save();
       }
     }
}
