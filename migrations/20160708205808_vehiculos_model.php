<?php

use \Flexio\Migration\Migration;

class VehiculosModel extends Migration
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
    public function change() {
        $this->dropTable('int_vehiculo');
        $tabla = $this->table('int_vehiculo');
        $tabla->addColumn('uuid_vehiculo', 'binary', array('limit' => 16))
                ->addColumn('empresa_id', 'integer', array('limit' => 10))
                ->addColumn('chasis', 'string', array('limit' => 100))
                ->addColumn('unidad', 'string', array('limit' => 100))
                ->addColumn('marca', 'string', array('limit' => 100))
                ->addColumn('modelo', 'string', array('limit' => 100))
                ->addColumn('placa', 'string', array('limit' => 100))
                ->addColumn('ano', 'string', array('limit' => 100))
                ->addColumn('motor', 'string', array('limit' => 100))
                ->addColumn('color', 'string', array('limit' => 100))
                ->addColumn('capacidad', 'string', array('limit' => 100))
                ->addColumn('estado', 'integer', array('limit' => 10))
                ->addColumn('operador', 'string', array('limit' => 100))
                ->addColumn('extras', 'string', array('limit' => 100))
                ->addColumn('valor_extras', 'string', array('limit' => 100))
                ->addColumn('porcentaje_acreedor', 'string', array('limit' => 100))
                ->addColumn('observaciones', 'text', array('limit' => 500))
                ->addColumn('updated_at', 'datetime')
                ->addColumn('created_at', 'datetime')
              ->save();
    }
}
