<?php

use \Flexio\Migration\Migration;

class OportunidadesV4 extends Migration
{
    
    public function up(){
        
        $this->table('opo_oportunidades_relaciones')
                ->addColumn('oportunidad_id','integer',['limit'=>11])
                ->addColumn('relacionable_id','integer',['limit'=>11])
                ->addColumn('relacionable_type','string',['limit'=>140])
                ->save();
        
    }
    
    public function down(){
        
        $this->dropTable('opo_oportunidades_relaciones');
        
    }
    
}
