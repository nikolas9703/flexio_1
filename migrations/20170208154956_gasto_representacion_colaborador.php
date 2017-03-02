<?php

use \Flexio\Migration\Migration;

class GastoRepresentacionColaborador extends Migration
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
        $table =$this->table("col_colaboradores");
        if(!$table->hasColumn("islr_gasto_representacion") && !$table->hasColumn("ss_gasto_representacion")){
            $table
                ->addColumn("islr_gasto_representacion", "float",['default'=>0])
                ->addColumn("ss_gasto_representacion", "float",['default'=>0])
                ->update();
        }
    }
}
