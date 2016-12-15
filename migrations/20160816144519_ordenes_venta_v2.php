<?php

use \Flexio\Migration\Migration;

class OrdenesVentaV2 extends Migration
{
    
    protected $tableName = 'lines_items';


    public function up(){
        
        $this->table($this->tableName)
                ->changeColumn('cantidad', 'decimal', ['scale'=>4,'precision'=>10,'default'=>0])
                ->changeColumn('cantidad2', 'decimal', ['scale'=>4,'precision'=>10,'default'=>0])
                ->changeColumn('cantidad_devolucion', 'decimal', ['scale'=>4,'precision'=>10,'default'=>0])
                ->save();
        
    }
    
    public function down(){
        
        $this->table($this->tableName)
                ->changeColumn('cantidad', 'integer', ['limit'=>10,'default'=>0])
                ->changeColumn('cantidad2', 'integer', ['limit'=>10,'default'=>0])
                ->changeColumn('cantidad_devolucion', 'integer', ['limit'=>10,'default'=>0])
                ->save();
        
    }
    
}
