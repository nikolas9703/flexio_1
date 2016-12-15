<?php

use \Flexio\Migration\Migration;

class CascoAereo extends Migration
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
        $tabla = $this->table('int_casco_aereo');
        $tabla->addColumn('empresa_id', 'integer', array('limit' => 10))
                ->addColumn('serie', 'string', array('limit' => 100))                
                ->addColumn('marca', 'string', array('limit' => 100))
                ->addColumn('modelo', 'string', array('limit' => 100))
                ->addColumn('matricula', 'string', array('limit' => 100)) 
                ->addColumn('valor', 'string', array('limit' => 100))
                ->addColumn('pasajeros', 'string', array('limit' => 100))
                ->addColumn('tripulacion', 'string', array('limit' => 100))                
                ->addColumn('observaciones', 'text', array('limit' => 500))
                ->addColumn('updated_at', 'datetime')
                ->addColumn('created_at', 'datetime')
              ->save();
    }
}
