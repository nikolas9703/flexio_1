<?php

use \Flexio\Migration\Migration;

class InventariosV4 extends Migration
{
    
    public function up(){
        
        $this->table('inv_items')
                ->addColumn('codigo_barra','string',['limit'=>140])
                ->save();
        
    }
    
    public function down(){
        
        $this->table('inv_items')
                ->removeColumn('codigo_barra')
                ->save();
        
    }
    
}
