<?php

use \Flexio\Migration\Migration;

class SegEditPlanesV1 extends Migration
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
        $tabla = $this->table('seg_planes');
        $tabla->addColumn('id_impuesto', 'integer', array('limit' => 10))
                ->removeColumn('comision')
                ->removeColumn('sobre_comision')
                ->save();
    }

    public function down()
    {
        $table = $this->table('seg_planes');
        $table  ->removeColumn('id_impuesto')
                ->addColumn('comision', 'integer', array('limit' => 8))
                ->addColumn('sobre_comision', 'integer', array('limit' => 8))
                ->save();
    }
}
