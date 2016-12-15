<?php

use \Flexio\Migration\Migration;

class InventariosV2 extends Migration
{
    
    public function up(){
        
        $this->table('atr_atributos')
                ->addColumn('nombre','string',['limit'=>140])
                ->addColumn('descripcion','string',['limit'=>140])
                ->addColumn('atributable_type','string',['limit'=>140])
                ->addColumn('atributable_id','integer',['limit'=>11])
                ->addColumn('created_at','datetime')
                ->addColumn('updated_at','datetime')
                ->save();
        
    }
    
    public function down(){
        
        $this->dropTable('atr_atributos')->save();
        
    }
    
}
