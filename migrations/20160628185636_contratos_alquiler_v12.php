<?php

use \Flexio\Migration\Migration;

class ContratosAlquilerV12 extends Migration
{
    
    public function up(){
        
        $this->table('conalq_contratos_alquiler')
                ->addColumn('corte_facturacion_id', 'integer', ['limit'=>11])
                ->addColumn('dia_corte', 'integer', ['limit'=>11])
                ->save();
        
    }
    
    public function down() {
        
        $this->table('conalq_contratos_alquiler')
                ->removeColumn('corte_facturacion_id')
                ->removeColumn('dia_corte')
                ->save();
        
    }
    
}
