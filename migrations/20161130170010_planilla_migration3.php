<?php

use \Flexio\Migration\Migration;

class PlanillaMigration3 extends Migration
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
     *$table->enum('tipo',['creado', 'actualizado'])->default('creado');
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
      */
    public function change()
    {
      $tabla = $this->table('pln_pagadas_colaborador');
      $tabla 
      ->addColumn('decimo_tercermes','enum',  array('values' => ['si', 'no'], 'default' => 'no'))
      ->addColumn('prima_antiguedad','enum',  array('values' => ['si', 'no'], 'default' => 'no'))
      ->addColumn('asistencia','enum',  array('values' => ['si', 'no'], 'default' => 'no'))
            ->update();
    }
}
