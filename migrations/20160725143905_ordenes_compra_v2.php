<?php

use \Flexio\Migration\Migration;

class OrdenesCompraV2 extends Migration
{
    
    public function up(){
        
        $this->table('ord_ordenes')
                ->changeColumn('numero', 'string', ['limit'=>140,'default'=>''])
                ->save();
        
    }
    
    public function down(){
        
        $this->table('ord_ordenes')
                ->changeColumn('numero', 'integer', ['limit'=>8])
                ->save();
        
    }
    
}
