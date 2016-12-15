<?php

use \Flexio\Migration\Migration;

class CargaModel extends Migration
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
        $tabla = $this->table('int_carga');
        $tabla->addColumn('empresa_id', 'integer', array('limit' => 10))
                ->addColumn('numero', 'string', array('limit' => 100))                
                ->addColumn('detalle', 'string', array('limit' => 100))                
                ->addColumn('no_liquidacion', 'string', array('limit' => 100))
                ->addColumn('fecha_despacho', 'date')
                ->addColumn('fecha_arribo', 'date')                
                ->addColumn('valor', 'string', array('limit' => 100))
                ->addColumn('tipo_empaque', 'integer', array('limit' => 10))                
                ->addColumn('condicion_envio', 'integer', array('limit' => 10))                
                ->addColumn('medio_transporte', 'integer', array('limit' => 10))
                ->addColumn('origen', 'string', array('limit' => 200))
                ->addColumn('destino', 'string', array('limit' => 200))                  
                ->addColumn('observaciones', 'text', array('limit' => 500))
                ->addColumn('updated_at', 'datetime')
                ->addColumn('created_at', 'datetime')
              ->save();
    }
}
