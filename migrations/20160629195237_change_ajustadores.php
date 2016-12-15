<?php

use \Flexio\Migration\Migration;

class ChangeAjustadores extends Migration
{
    public function up() {
        $this->_table_ajustadores();         
    }
    public function down() {
        $this->dropTable('seg_ajustadores');        
    }
    
    private function _table_ajustadores() {
        $table = $this->table('seg_ajustadores');
        $table->addColumn('uuid_ajustadores', 'binary', array('limit' => 16))
                ->addColumn('identificacion', 'integer', array('limit' => 10))
                ->addColumn('nombre', 'string', array('limit' => 100))                
                ->addColumn('ruc', 'string', array('limit' => 100))
                ->addColumn('telefono', 'string', array('limit' => 50))
                ->addColumn('email', 'string', array('limit' => 50))                
                ->addColumn('direccion', 'string', array('limit' => 50))                
                ->addColumn('empresa_id', 'integer', array('limit' => 10))
                ->addColumn('updated_at', 'datetime')
                ->addColumn('created_at', 'datetime')
                ->addColumn('estado_id', 'integer', array('limit' => 10))                
                ->save();
    }
}
