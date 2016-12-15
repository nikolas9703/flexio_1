<?php

use \Flexio\Migration\Migration;

class OrdenesTrabajoV2 extends Migration
{
    
    public function up(){
        
        $exists = $this->hasTable('odt_ordenes_trabajo');
        
        if(!$exists){
            
            $this->table('odt_ordenes_trabajo')
                ->addColumn('uuid_orden_trabajo', 'binary', ["limit" => 16])
	    	->addColumn('numero', 'string', ["limit" => 100])
	    	->addColumn('empresa_id', 'integer', ["limit" => 10])
	    	->addColumn('cliente_id', 'integer', ["limit" => 10])
	    	->addColumn('centro_id', 'integer', ["limit" => 10])
	    	->addColumn('tipo_orden_id', 'integer', ["limit" => 10])
	    	->addColumn('estado_id', 'integer', ["limit" => 10])
	    	->addColumn('orden_de_id', 'integer', ["limit" => 10])
	    	->addColumn('orden_de', 'string', ["limit" => 45])
	    	->addColumn('lista_precio_id', 'integer', ["limit" => 10])
	    	->addColumn('bodega_id', 'integer', ["limit" => 10])
	    	->addColumn('fecha_inicio', 'datetime')
	    	->addColumn('fecha_planificada_fin', 'datetime')
	    	->addColumn('fecha_real_fin', 'datetime')
	    	->addColumn('creado_por', 'integer', ["limit" => 10])
	    	->addColumn('created_at', 'datetime')
	    	->addColumn('updated_at', 'datetime')
                ->addColumn('identificador', 'string', ["limit" => 140])
                ->addColumn('etiqueta', 'string', ["limit" => 140])
    		->save();
            
        }
        
        
    }
    
    public function down(){
        
        $this->dropTable('odt_ordenes_trabajo');
        
    }
    
}
