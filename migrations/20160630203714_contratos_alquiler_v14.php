<?php

use \Flexio\Migration\Migration;

class ContratosAlquilerV14 extends Migration
{
    
    public function up(){
        
        $this->table('cotz_cotizaciones')
                ->addColumn('cliente_tipo','string',['limit'=>100,'default'=>'cliente'])//cliente || cliente_potencial
                ->addColumn('tipo','string',['limit'=>100,'default'=>'venta'])
                ->addColumn('centro_contable_id','integer',['limit'=>11])
                ->save();
        
    }
    
    
    public function down(){
        
        $this->table('cotz_cotizaciones')
                ->removeColumn('cliente_tipo')
                ->removeColumn('tipo')
                ->removeColumn('centro_contable_id')
                ->save();
        
    }
    
}
