<?php

use \Flexio\Migration\Migration;

class AddDevolucionAlquier1Migration extends Migration
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
        $tabla = $this->table('contratos_items_detalles');
        $tabla->addColumn('estado_item_devuelto','string', array('limit' => 255, 'null' => NULL));
        $tabla->addColumn('bodega_retorno_id','integer', array('limit' => 10))
        ->save();
    }
}
