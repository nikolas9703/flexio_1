<?php

use \Flexio\Migration\Migration;

class PedidosV1 extends Migration
{
    
    protected $tableName = 'ped_pedidos';
    
    public function up(){
      
        $this->table($this->tableName)
                ->addColumn('observaciones', 'string', ['limit'=>300,'default'=>''])
                ->changeColumn('numero', 'string', ['limit'=>140,'default'=>''])
                ->save();
        
    }
    
    public function down(){
      
        $this->table($this->tableName)
                ->removeColumn('observaciones')
                ->changeColumn('numero', 'integer', ['limit'=>10,'default'=>0])
                ->save();
        
    }
    
}
