<?php

use \Flexio\Migration\Migration;

class OportunidadesV1 extends Migration
{
    
    public function up(){
        
        $this->table('opo_oportunidades')
                ->addColumn('codigo','string',['limit'=>140])
                ->addColumn('empresa_id','integer',['limit'=>11])
                ->addColumn('uuid_oportunidad','binary',['limit'=>16])
                ->addColumn('created_at','datetime')
                ->addColumn('updated_at','datetime')
                ->addColumn('cliente_id','integer',['limit'=>11])
                ->addColumn('nombre','string',['limit'=>140])
                ->addColumn('monto','decimal',['scale'=>2,'precision'=>10])
                ->addColumn('fecha_cierre','datetime')
                ->addColumn('asignado_a_id','integer',['limit'=>11])
                ->addColumn('etapa_id','integer',['limit'=>11])
                ->save();
        
    }
    
    public function down(){
        
        $this->dropTable('opo_oportunidades');
        
    }
    
}
