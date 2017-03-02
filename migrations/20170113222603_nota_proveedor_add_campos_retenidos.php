<?php

use \Flexio\Migration\Migration;

class NotaProveedorAddCamposRetenidos extends Migration
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
        $lines_items = $this->table('compra_nota_debitos');
        $lines_items->addColumn('retenido', 'decimal', ['scale' => 4, 'precision' => 21,'default'=>0])
                    ->addColumn('monto_retenido', 'decimal', ['scale' => 4, 'precision' => 21,'default'=>0])
                    ->save();
    }
}
