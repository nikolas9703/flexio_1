<?php

use \Flexio\Migration\Migration;

class IntUbicacionV1 extends Migration
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
        $tabla = $this->table('int_ubicacion');
        $tabla->addColumn('empresa_id', 'integer', array('limit' => 10))
                ->addColumn('numero', 'string', array('limit' => 100))                
                ->addColumn('nombre', 'string', array('limit' => 100))                
                ->addColumn('direccion', 'string', array('limit' => 200))                
                ->addColumn('edif_mejoras', 'string', array('limit' => 100))                                
                ->addColumn('contenido', 'string', array('limit' => 100))
                ->addColumn('maquinaria', 'string', array('limit' => 100))
                ->addColumn('inventario', 'string', array('limit' => 100))
                ->addColumn('acreedor', 'string', array('limit' => 100))
                ->addColumn('porcentaje_acreedor', 'string', array('limit' => 100))
                ->addColumn('observaciones', 'string', array('limit' => 500))
                ->addColumn('estado', 'integer', array('limit' => 10))
                ->addColumn('tipo_id','integer', array('limit' => 10))
                ->addColumn('updated_at', 'datetime')
                ->addColumn('created_at', 'datetime')
                ->save();
    }

    public function down()
    {
        $this->dropTable('int_ubicacion');
    }
}
