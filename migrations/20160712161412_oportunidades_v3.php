<?php

use \Flexio\Migration\Migration;

class OportunidadesV3 extends Migration
{
    
    public function up(){
        
        $data = [
            ['id'=>'1','nombre'=>'Prospecto','valor'=>'prospecto','tipo'=>'estado'],
            ['id'=>'2','nombre'=>'En negociaci&oacute;n','valor'=>'en_negociacion','tipo'=>'estado'],
            ['id'=>'3','nombre'=>'Ganada','valor'=>'ganada','tipo'=>'estado'],
            ['id'=>'4','nombre'=>'Perdida','valor'=>'perdida','tipo'=>'estado'],
            ['id'=>'5','nombre'=>'Anulada','valor'=>'anulada','tipo'=>'estado'],
        ];
        $this->insert('opo_oportunidades_catalogos', $data);
        
    }
    
    public function down(){
        
        $this->execute('TRUNCATE opo_oportunidades_catalogos');
        
    }
    
}
