<?php

use \Flexio\Migration\Migration;

class FecharansaccionMigration extends Migration
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
    /*public function change()
    {

    }*/
    public function up()
    {
        $table = $this->table('pln_pagadas_ingresos');
        $table
                ->addColumn('updated_at', 'datetime', array('after'=>'beneficio_monto'))
                ->addColumn('created_at', 'datetime', array('after'=>'beneficio_monto'))
                ->addColumn('fecha_transaccion','datetime', array('after'=>'beneficio_monto'))
                ->save();
    }
}
