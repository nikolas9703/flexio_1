<?php

use \Flexio\Migration\Migration;

class InventariosV5 extends Migration
{
    
    public function up(){
        
        $this->execute("DELETE FROM devalq_devoluciones_alquiler_catalogos WHERE id ='4'");
        $this->execute("UPDATE devalq_devoluciones_alquiler_catalogos SET nombre='Devuelto', valor='devuelto' WHERE id='2'");
        
    }
    
    public function down() {
        
        //...
        
    }
    
}
