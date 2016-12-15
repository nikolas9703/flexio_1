<?php

use \Flexio\Migration\Migration;

class OportunidadesV5 extends Migration
{
    
    public function up(){
        
        $this->table('opo_oportunidades')
                ->addColumn('cliente_tipo','string',['limit'=>140,'default'=>'cliente'])
                ->save();
        
    }
    
    public function down() {
        
        $this->table('opo_oportunidades')
                ->removeColumn('cliente_tipo')
                ->save();
        
    }
    
}
