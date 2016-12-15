<?php

use \Flexio\Migration\Migration;

class AddCatalogoPagos extends Migration
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
        $data = [
            ['key'=>'1','valor'=>'Pagar de cuenta de banco','etiqueta'=>'banco', 'tipo' => 'tipo_pago', 'orden' => '1'],
            ['key'=>'1','valor'=>'Pagar de caja','etiqueta'=>'caja', 'tipo' => 'tipo_pago', 'orden' => '2'],
        ];
        $this->insert('pag_pagos_catalogo', $data);
    }
}
