<?php

use \Flexio\Migration\Migration;

class ContratosAlquilerV11 extends Migration
{
    
    public function up()
    {
        $this->table('contratos_items_detalles')
                ->addColumn('created_at','datetime')
                ->addColumn('updated_at','datetime')
                ->save();
    }
    
    public function down()
    {
        
        $this->table('contratos_items_detalles')
                ->removeColumn('created_at')
                ->removeColumn('updated_at')
                ->save();
        
    }
    
}
