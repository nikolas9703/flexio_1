<?php

use \Flexio\Migration\Migration;

class CotizacionesV2 extends Migration
{
    
    public function up(){
        
        $this->table('lines_items')
                ->changeColumn('atributo_id','integer',['limit'=>11,'default'=>0,'null'=>true])
                ->save();
        
    }
    
    public function down(){
        
        $this->table('lines_items')
                ->changeColumn('atributo_id','integer',['limit'=>11,'default'=>0,'null'=>false])
                ->save();
        
    }
    
}
