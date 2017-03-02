<?php

use Phinx\Migration\AbstractMigration;

class ChangeColumnTypeLinesItems extends AbstractMigration
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
        $lines_items = $this->table('lines_items');
        $lines_items->changeColumn('precio_unidad', 'decimal', ['scale' => 5, 'precision' => 21])
                    ->changeColumn('impuesto_total', 'decimal', ['scale' => 5, 'precision' => 21])
                    ->changeColumn('precio_total', 'decimal', ['scale' => 5, 'precision' => 21])
                    ->save();

        $faccom = $this->table('faccom_facturas_items');
        $faccom->changeColumn('precio_unidad', 'decimal', ['scale' => 5, 'precision' => 21])
                ->changeColumn('subtotal', 'decimal', ['scale' => 5, 'precision' => 21])
                ->changeColumn('total', 'decimal', ['scale' => 5, 'precision' => 21])
                ->changeColumn('impuestos', 'decimal', ['scale' => 5, 'precision' => 21])
                ->save();

    }
}
