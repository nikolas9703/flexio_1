<?php

use \Flexio\Migration\Migration;

class OrdenesVentaDefaultValue extends Migration
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
    /**
     * Migrate Up.
     */
    public function up()
    {
        $users = $this->table('ord_ordenes');
        $users->changeColumn('modo_pago_id', 'string', array('limit' => 255, 'null' => NULL))
              ->changeColumn('dias', 'integer', array('limit' => 255, 'null' => NULL))
            ->save();
    }
}
