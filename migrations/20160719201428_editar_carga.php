<?php

use \Flexio\Migration\Migration;

class EditarCarga extends Migration
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
    public function up() {
        $table = $this->table('int_carga');
        $table->changeColumn('detalle', 'string', array('limit' => 100, 'null' => true))            
                ->changeColumn('fecha_despacho', 'date', array('null' => true))              
                ->changeColumn('fecha_arribo', 'date', array('null' => true))               
                ->changeColumn('valor', 'string', array('limit' => 100, 'null' => true))               
                ->changeColumn('tipo_empaque', 'integer', array('limit' => 10, 'null' => true))               
                ->changeColumn('condicion_envio', 'integer', array('limit' => 10, 'null' => true))               
                ->changeColumn('medio_transporte', 'integer', array('limit' => 10, 'null' => true))              
                ->changeColumn('origen', 'string', array('limit' => 100, 'null' => true))              
                ->changeColumn('destino', 'string', array('limit' => 100, 'null' => true))                
                ->changeColumn('observaciones', 'string', array('limit' => 200, 'null' => true))          
                ->addColumn('estado', 'integer', array('limit' => 10, 'null' => true))        
                ->changeColumn('tipo_id', 'integer', array('limit' => 10, 'null' => true))             
                ->changeColumn('tipo_obligacion', 'string', array('limit' => 100, 'null' => true))              
                ->changeColumn('acreedor', 'string', array('limit' => 100, 'null' => true))
                ->update();
    }
}
