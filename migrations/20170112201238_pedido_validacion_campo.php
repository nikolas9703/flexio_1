<?php

use \Flexio\Migration\Migration;

class PedidoValidacionCampo extends Migration
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
        $table = $this->table('ped_pedidos');
        $column = $table->hasColumn('validado');
        if (!$column) {
          $table->addColumn('validado', 'string', array('limit' => 10, 'default'=>'no'))->save();
        }
      }
}
 
