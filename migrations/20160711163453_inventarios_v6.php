<?php

use \Flexio\Migration\Migration;

class InventariosV6 extends Migration
{
    
    public function up(){
        
        $this->table('inv_items')
                ->addColumn('item_alquiler','integer',['limit'=>2])
                ->addColumn('tarifa_hora','decimal',['scale'=>2,'precision'=>10])
                ->addColumn('tarifa_diario','decimal',['scale'=>2,'precision'=>10])
                ->addColumn('tarifa_mensual','decimal',['scale'=>2,'precision'=>10])
                ->save();
        
    }
    
    public function down(){
        
        $this->table('inv_items')
                ->removeColumn('item_alquiler')
                ->removeColumn('tarifa_hora')
                ->removeColumn('tarifa_diario')
                ->removeColumn('tarifa_mensual')
                ->save();
        
    }
    
}
