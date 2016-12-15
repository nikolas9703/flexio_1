<?php

use \Flexio\Migration\Migration;

class ContratosAlquilerV9 extends Migration
{
    
    public function up()
    {
        $this->table('contratos_items_detalles')
                ->addColumn('operacion_type','string',['limit'=>100])
                ->addColumn('operacion_id','integer',['limit'=>11])
                ->addColumn('relacion_type','string',['limit'=>100])
                ->addColumn('relacion_id','integer',['limit'=>11])
                ->addColumn('cantidad','integer',['limit'=>11])
                ->addColumn('serie','string',['limit'=>100])
                ->addColumn('bodega_id','integer',['limit'=>11])
                ->addColumn('fecha','datetime')
                ->save();
    }
    
    
    public function down()
    {
        $this->dropTable('contratos_items_detalles');
    }
    
    
}
