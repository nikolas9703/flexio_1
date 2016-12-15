<?php

use \Flexio\Migration\Migration;

class SegPlanesComisionesV1 extends Migration
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
        $tabla = $this->table('seg_planes_comisiones');
        $tabla->addColumn('uuid_comisiones', 'binary', array('limit' => 16))
                ->addColumn('inicio', 'string', array('limit' => 10))                
                ->addColumn('fin', 'string', array('limit' => 10))                
                ->addColumn('comision', 'string', array('limit' => 200))                
                ->addColumn('sobre_comision', 'string', array('limit' => 100))
                ->addColumn('id_planes', 'integer', array('limit' => 10))
                ->addColumn('updated_at', 'datetime')
                ->addColumn('created_at', 'datetime')
                ->save();
    }

    public function down()
    {
        $this->dropTable('seg_planes_comisiones');
    }
}
