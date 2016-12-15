<?php

use \Flexio\Migration\Migration;

class OrdenesTrabajoV3 extends Migration
{
    public function up(){
        
        $exists = $this->hasTable('odt_cat');
        
        if(!$exists){
            
            $this->table('odt_cat')
                ->addColumn('nombre', 'string', ["limit" => 100])
                ->addColumn('valor', 'string', ["limit" => 100])
                ->addColumn('tipo', 'string', ["limit" => 100])
                ->save();
            
        }
        
        
    }
    
    public function down(){
        
        $this->dropTable('odt_cat');
        
    }
}
