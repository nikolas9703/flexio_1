<?php

use \Flexio\Migration\Migration;

class PedidosV2 extends Migration
{
    
    protected $tableName = 'ped_pedidos_inv_items';
    
    public function up(){
      
        $this->table($this->tableName)
                ->addColumn('atributo_text', 'string', ['limit'=>140,'default'=>''])
                ->addColumn('atributo_id', 'integer', ['limit'=>11,'default'=>0])
                ->save();
        
    }
    
    public function down(){
      
        $this->table($this->tableName)
                ->removeColumn('atributo_text')
                ->removeColumn('atributo_id')
                ->save();
        
    }
    
}
