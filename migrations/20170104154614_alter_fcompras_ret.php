<?php

use \Flexio\Migration\Migration;

class AlterFcomprasRet extends Migration
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
      $table = $this->table('faccom_facturas');
      $table
              ->changeColumn('porcentaje_retencion', 'decimal', array('scale' => 7, 'precision' => 15, 'default' => '0.00'))
              ->save();
    }
}
