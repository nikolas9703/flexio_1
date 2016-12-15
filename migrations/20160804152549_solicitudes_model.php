<?php

use \Flexio\Migration\Migration;

class SolicitudesModel extends Migration
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
        $tabla = $this->table('seg_solicitudes');
        $tabla->addColumn('uuid_solicitudes', 'binary', array('limit' => 16))
                ->addColumn('numero', 'string', array('limit' => 100))
                ->addColumn('cliente_id', 'integer', array('limit' => 11))                
                ->addColumn('aseguradora_id', 'integer', array('limit' => 11, 'null' => true))
                ->addColumn('ramo', 'string', array('limit' => 50))
                ->addColumn('id_tipo_poliza', 'integer', array('limit' => 10))
                ->addColumn('usuario_id', 'integer', array('limit' => 10))
                ->addColumn('estado', 'integer', array('limit' => 10))
                ->addColumn('updated_at', 'datetime')
                ->addColumn('created_at', 'datetime')
              ->save();
    }
}
