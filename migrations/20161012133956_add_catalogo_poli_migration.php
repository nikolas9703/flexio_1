<?php

use \Flexio\Migration\Migration;

class AddCatalogoPoliMigration extends Migration
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

    public function up()
    {

        $rows = [
            [
              'key'  => 5,
              'valor' => '13-20',
              'etiqueta' => 'Por aprobar - Suspendia',
              'tipo' => 'factura_compra',
              'estado1' => '13',
              'estado2' => '20',
              'orden' => 1
            ],
         ];

        $this->insert('ptr_transacciones_catalogo', $rows);
    }
}
