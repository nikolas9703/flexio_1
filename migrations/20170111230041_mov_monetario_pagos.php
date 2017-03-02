<?php

use \Flexio\Migration\Migration;

class MovMonetarioPagos extends Migration
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
       $pagos = $this->table('pag_pagos');
       $pagos->changeColumn('formulario', 'enum', array('values' => ['factura', 'proveedor', 'subcontrato','planilla','pago_extraordinario','caja','transferencia','movimiento_monetario']))
             ->save();
     }
}
