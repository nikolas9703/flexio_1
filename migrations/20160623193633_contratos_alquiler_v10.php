<?php

use \Flexio\Migration\Migration;

class ContratosAlquilerV10 extends Migration
{
    
    
    public function up()
    {
        $this->table('entalq_entregas_alquiler')
                ->addColumn('observaciones', 'string', ['limit'=>300])
                ->save();
    }
    
    public function down()
    {
        $this->table('entalq_entregas_alquiler')
                ->removeColumn('observaciones')
                ->save();
    }
    
    
}
