<?php

use \Flexio\Migration\Migration;

class OrdenesComprasV3 extends Migration
{
    
    public function up(){
        
        $this->table('lines_items')
                ->addColumn('atributo_text', 'string', ['limit'=>140])
                ->save();
        
    }
    
    public function down(){
        
        $this->table('lines_items')
                ->removeColumn('atributo_text')
                ->save();
        
    }
    
}
