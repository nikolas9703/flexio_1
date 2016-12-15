<?php

use \Flexio\Migration\Migration;

class ContratosAlquilerAddMigration extends Migration
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
        $tabla = $this->table('contratos_items')
        ->addColumn('impuesto','float',  array('limit' => 100))
        ->addColumn('descuento','float',  array('limit' => 100))
        ->addColumn('cuenta_id','integer',  array('limit' => 10))
        ->update();
    }
}
