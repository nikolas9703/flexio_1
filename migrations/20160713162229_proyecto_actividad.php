<?php

use \Flexio\Migration\Migration;

class ProyectoActividad extends Migration
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
        $tabla = $this->table('int_proyecto_actividad');
        $tabla->addColumn('empresa_id', 'integer', array('limit' => 10))
                ->addColumn('numero', 'string', array('limit' => 100))                
                ->addColumn('nombre_proyecto', 'string', array('limit' => 100))                
                ->addColumn('no_orden', 'string', array('limit' => 100))                
                ->addColumn('contratista', 'string', array('limit' => 100))
                ->addColumn('representante_legal', 'string', array('limit' => 100))                                
                ->addColumn('duracion', 'string', array('limit' => 100))
                ->addColumn('fecha', 'date')
                ->addColumn('monto', 'string', array('limit' => 100))
                ->addColumn('monto_afianzado', 'string', array('limit' => 100))                
                ->addColumn('acreedor', 'integer', array('limit' => 10))                
                ->addColumn('porcentaje_acreedor', 'integer', array('limit' => 10))                
                ->addColumn('ubicacion', 'string', array('limit' => 200))                
                ->addColumn('observaciones', 'text', array('limit' => 500))
                ->addColumn('updated_at', 'datetime')
                ->addColumn('created_at', 'datetime')
              ->save();
    }
}
