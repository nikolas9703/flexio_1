<?php

use \Flexio\Migration\Migration;

class SubcontratosV3 extends Migration
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
        $table = $this->table("cont_adendas_montos");
        $table->addColumn("cuenta_id", "integer", ["limit" => 10])
            ->update();
        
        $table = $this->table("sub_adendas_montos");
        $table->addColumn("cuenta_id", "integer", ["limit" => 10])
            ->update();
    }
}
