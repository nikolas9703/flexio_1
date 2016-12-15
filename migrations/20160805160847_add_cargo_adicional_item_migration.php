<?php

use \Flexio\Migration\Migration;

class AddCargoAdicionalItemMigration extends Migration
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
    	$tabla = $this->table('lines_items');
    	$tabla->addColumn('item_adicional','boolean', [
    		'default' => false,
    		'null' => true,
    	]);
    	$tabla->addColumn('periodo_tarifario_id','integer', array('limit' => 10))->save();
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    	$tabla = $this->table('lines_items');
    	$tabla->removeColumn('item_adicional');
    	$tabla->removeColumn('periodo_tarifario_id')->save();
    }
}
