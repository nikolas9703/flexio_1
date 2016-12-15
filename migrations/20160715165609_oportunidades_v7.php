<?php

use \Flexio\Migration\Migration;

class OportunidadesV7 extends Migration
{
    
    public function up(){
        
        $this->execute("UPDATE opo_oportunidades_catalogos SET nombre='".  utf8_decode("En negociación")."' WHERE id = '2'");
        
    }
    
    public function down(){
        
        //...
        
    }
    
}
