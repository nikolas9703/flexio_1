<?php

use \Flexio\Migration\Migration;

class ContratosAlquilerV13 extends Migration
{
    
    public function up(){
        
        $rows = [
            ['id'=>'9','nombre'=>'Sin corte','valor'=>'sin_corte','tipo'=>'corte_facturacion'],
            ['id'=>'10','nombre'=>'Diario','valor'=>'diario','tipo'=>'corte_facturacion'],
            ['id'=>'11','nombre'=>'Mensual','valor'=>'mensual','tipo'=>'corte_facturacion']
        ];
        
        $this->insert('conalq_contratos_alquiler_catalogos', $rows);
        
    }        
    
    public function down() {
        
        $this->execute('DELETE FROM conalq_contratos_alquiler_catalogos WHERE id IN (9,10,11)');
        
    }
    
}
