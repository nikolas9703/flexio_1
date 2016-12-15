<?php

use \Flexio\Migration\Migration;

class AddFechaCierre4 extends Migration
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
        $tabla = $this->table('pln_pagadas_colaborador');
        $column = $tabla->hasColumn('fecha_cierre_planilla');

        if (!$column) {
          $tabla->addColumn('fecha_cierre_planilla', 'date', array('limit' => 16))->update();
        }
    }

}
