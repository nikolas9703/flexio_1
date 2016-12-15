<?php

use \Flexio\Migration\Migration;

class ContratosAlquilerV15 extends Migration
{
    
    public function up(){
        
        $this->table('cotz_cotizables')
                ->addColumn('cotizacion_id','integer',['limit'=>11])
                ->addColumn('cotz_cotizable_id','integer',['limit'=>11])
                ->addColumn('cotz_cotizable_type','string',['limit'=>140])
                ->save();
        
    }
    
    public function down(){
        
        $this->dropTable('cotz_cotizables')
                ->save();
        
    }
    
}
