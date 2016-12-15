<?php

use \Flexio\Migration\Migration;

class OportunidadesV6 extends Migration
{
    
    public function up(){
        
        $this->table('opo_oportunidades_relaciones')
                ->addColumn('created_at','datetime')
                ->addcolumn('updated_at','datetime')
                ->save();
        
    }
    
    public function down(){
        
        $this->table('opo_oportunidades_relaciones')
                ->removeColumn('created_at')
                ->removeColumn('updated_at')
                ->save();
        
    }
    
}
