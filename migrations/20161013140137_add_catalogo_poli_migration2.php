<?php

use \Flexio\Migration\Migration;

class AddCatalogoPoliMigration2 extends Migration
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
               'key'  => 6,
               'valor' => '20-13',
               'etiqueta' => 'Suspendia - Por aprobar',
               'tipo' => 'factura_compra',
               'estado1' => '20',
               'estado2' => '13',
               'orden' => 1
             ],
          ];

         $this->insert('ptr_transacciones_catalogo', $rows);
     }
}
