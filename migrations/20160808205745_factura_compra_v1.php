<?php

use \Flexio\Migration\Migration;

class FacturaCompraV1 extends Migration
{
    
    public function up(){
        
        $this->table('faccom_facturas_items')
                ->addColumn('atributo_id', 'integer', ['limit'=>140,'default'=>0])
                ->addColumn('atributo_text', 'string', ['limit'=>140,'default'=>''])
                ->save();
        
    }
    
    public function down(){
        
        $this->table('faccom_facturas_items')
                ->removeColumn('atributo_id')
                ->removeColumn('atributo_text')
                ->save();
        
    }
    
}
