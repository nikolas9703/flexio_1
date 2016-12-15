<?php

use \Flexio\Migration\Migration;

class OportunidadesV2 extends Migration
{
    
    public function up(){
        
        $this->table('opo_oportunidades_catalogos')
                ->addColumn('nombre','string',['limit'=>140])
                ->addColumn('valor','string',['limit'=>140])
                ->addColumn('tipo','string',['limit'=>140])
                ->save();
        
    }
    
    public function down(){
        
        $this->dropTable('opo_oportunidades_catalogos');
        
    }
    
}
