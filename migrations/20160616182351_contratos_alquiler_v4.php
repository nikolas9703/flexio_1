<?php

use \Flexio\Migration\Migration;

class ContratosAlquilerV4 extends Migration
{
    
    public function up()
    {
        $this->table('conalq_contratos_alquiler')
                ->addColumn('observaciones', 'string', ['limit'=>140])
                ->addColumn('referencia', 'string', ['limit'=>140])
                ->addColumn('centro_facturacion_id', 'integer', ['limit'=>11])
                ->save();
        
        $this->table('contratos_items')
                ->addColumn('contratable_type', 'string', ['limit'=>200])
                ->addColumn('contratable_id', 'integer', ['limit'=>11])
                ->addColumn('categoria_id', 'integer', ['limit'=>11])
                ->addColumn('item_id', 'integer', ['limit'=>11])
                ->addColumn('cantidad', 'integer', ['limit'=>11])
                ->addColumn('entregado', 'integer', ['limit'=>11])
                ->addColumn('devuelto', 'integer', ['limit'=>11])
                ->addColumn('en_alquiler', 'integer', ['limit'=>11])
                ->addColumn('ciclo_id', 'integer', ['limit'=>11])
                ->addColumn('tarifa', 'decimal', ['scale' => 2, 'precision' => 10])
                ->save();
    }
    
    
    public function down()
    {
        $this->table('conalq_contratos_alquiler')
                ->removeColumn('observaciones')
                ->removeColumn('referencia')
                ->removeColumn('centro_facturacion_id')
                ->save();
        
        $this->dropTable('contratos_items');
    }
    
}
