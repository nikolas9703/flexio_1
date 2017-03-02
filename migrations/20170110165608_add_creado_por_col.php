<?php

use \Flexio\Migration\Migration;

class AddCreadoPorCol extends Migration
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
         $tabla = $this->table('sub_subcontratos');
        $tabla->addColumn('creado_por', 'integer', array('limit' => 10, 'after'=>'monto_subcontrato'))
         ->update();
     }
}
