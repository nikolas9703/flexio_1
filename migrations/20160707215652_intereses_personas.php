<?php

use \Flexio\Migration\Migration;

class InteresesPersonas extends Migration
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
        $tabla = $this->table('int_personas');
        $tabla->addColumn('uuid_persona', 'binary', array('limit' => 16))
                ->addColumn('numero', 'string', array('limit' => 100))
                ->addColumn('nombre', 'string', array('limit' => 100))                
                ->addColumn('identificacion', 'string', array('limit' => 100))
                ->addColumn('fecha_nacimiento', 'string', array('limit' => 50))
                ->addColumn('edad', 'integer', array('limit' => 10))                
                ->addColumn('estado_civil', 'integer', array('limit' => 10))                
                ->addColumn('nacionalidad', 'string', array('limit' => 100))
                ->addColumn('sexo', 'integer', array('limit' => 10))
                ->addColumn('estatura', 'string', array('limit' => 100))
                ->addColumn('peso', 'string', array('limit' => 100))
                ->addColumn('telefono_residencial', 'string', array('limit' => 100))
                ->addColumn('telefono_oficina', 'string', array('limit' => 100))
                ->addColumn('direccion_residencial', 'string', array('limit' => 100))
                ->addColumn('direccion_laboral', 'string', array('limit' => 100))
                ->addColumn('observaciones', 'string', array('limit' => 500))
                ->addColumn('estado', 'integer', array('limit' => 10))
                ->addColumn('updated_at', 'datetime')
                ->addColumn('created_at', 'datetime')
              ->save();
    }
}
