<?php

use \Flexio\Migration\Migration;

class PlanillaLiquidacionMigration extends Migration
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
    public function up()
    {
    	$this->_table_pagadas_colaborador();
    }
    private function _table_pagadas_colaborador() {
    	$table = $this->table('pln_pagadas_colaborador');
    	$table->addColumn('contrato_id', 'integer', array('limit' => 10))
    	 
    	->save();
    }
}
