<?php

use \Flexio\Migration\Migration;

class EditarProyectos extends Migration
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
        $tabla->changeColumn('empresa_id', 'integer', array('limit' => 10))
                ->changeColumn('numero', 'string', array('limit' => 100))                
                ->changeColumn('nombre_proyecto', 'string', array('limit' => 100))                
                ->changeColumn('no_orden', 'string', array('limit' => 100, 'null' => true))                
                ->changeColumn('contratista', 'string', array('limit' => 100, 'null' => true))
                ->changeColumn('representante_legal', 'string', array('limit' => 100, 'null' => true))                                
                ->changeColumn('duracion', 'string', array('limit' => 100, 'null' => true))
                ->changeColumn('fecha', 'date', array('null' => true))
                ->changeColumn('monto', 'string', array('limit' => 100, 'null' => true))
                ->changeColumn('monto_afianzado', 'string', array('limit' => 100, 'null' => true))                
                ->changeColumn('acreedor', 'integer', array('limit' => 10, 'null' => true))
                ->changeColumn('ubicacion', 'string', array('limit' => 200, 'null' => true))                
                ->changeColumn('observaciones', 'text', array('limit' => 500, 'null' => true))
                ->changeColumn('updated_at', 'datetime')
                ->changeColumn('created_at', 'datetime')
              ->save();
    }
}
