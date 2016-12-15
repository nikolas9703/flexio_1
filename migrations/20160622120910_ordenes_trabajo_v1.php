<?php

use \Flexio\Migration\Migration;

class OrdenesTrabajoV1 extends Migration
{
    public function up()
    {
    	$this->_table_ordenes_trabajo();
    	//$this->_table_ordenes_catalogo();
    }

    private function _table_ordenes_trabajo()
    {
    	/*$table = $this->table('odt_ordenes_trabajo');
    	$table->addColumn('uuid_orden_trabajo', 'binary', ["limit" => 16])
	    	->addColumn('numero', 'string', ["limit" => 100])
	    	->addColumn('empresa_id', 'integer', ["limit" => 10])
	    	->addColumn('cliente_id', 'integer', ["limit" => 10])
	    	->addColumn('centro_id', 'integer', ["limit" => 10])
	    	->addColumn('tipo_orden_id', 'integer', ["limit" => 10])
	    	->addColumn('estado_id', 'integer', ["limit" => 10])
	    	->addColumn('orden_de_id', 'integer', ["limit" => 10])
	    	->addColumn('orden_de', 'string', ["limit" => 45])
	    	->addColumn('lista_precio_id', 'integer', ["limit" => 10])
	    	->addColumn('lista_precio_id', 'integer', ["limit" => 10])
	    	->addColumn('bodega_id', 'integer', ["limit" => 10])
	    	->addColumn('fecha_inicio', 'datetime')
	    	->addColumn('fecha_planificada_fin', 'datetime')
	    	->addColumn('fecha_real_fin', 'datetime')
	    	->addColumn('creado_por ', 'integer', ["limit" => 10])
	    	->addColumn('created_at', 'datetime')
	    	->addColumn('updated_at', 'datetime')
    		->save();*/
    }

    private function _table_ordenes_trabajo_duplicado()
    {
    	/*$table2 = $this->table('odt_cat');
    	$table2->addColumn('nombre', 'string', ["limit" => 100])
		    	->addColumn('valor', 'string', ["limit" => 100])
		    	->addColumn('tipo', 'string', ["limit" => 100])
		    	->save();*/
    }
}
