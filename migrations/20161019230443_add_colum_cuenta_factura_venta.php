<?php

use \Flexio\Migration\Migration;

class AddColumCuentaFacturaVenta extends Migration
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
        $tabla = $this->table('fac_facturas');

        $column = $tabla->hasColumn('cuenta');
        if (!$column) {
            $tabla->addColumn('cuenta', 'integer', array('limit' => 11, 'null' => true))->update();
        }
    }
}
