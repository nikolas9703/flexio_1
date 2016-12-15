<?php

use \Flexio\Migration\Migration;

class OrdenesVentaV3 extends Migration
{
    
    protected $tableName = 'faccom_facturas_items';


    public function up(){
        
        $this->table($this->tableName)
                ->changeColumn('cantidad', 'decimal', ['scale'=>4,'precision'=>10,'default'=>0])
                ->save();
        
    }
    
    public function down(){
        
        $this->table($this->tableName)
                ->changeColumn('cantidad', 'integer', ['limit'=>10,'default'=>0])
                ->save();
        
    }
    
}
