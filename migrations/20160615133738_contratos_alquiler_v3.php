<?php

use \Flexio\Migration\Migration;

class ContratosAlquilerV3 extends Migration
{
    public function up()
    {
        $table = $this->table('entalq_entregas_alquiler');
        $table
                ->addColumn('uuid_entrega_alquiler', 'binary', array('limit' => 16))
                ->addColumn('codigo', 'string', array('limit' => 100))
                ->addColumn('fecha_entrega', 'datetime')
                ->addColumn('entregable_id', 'integer', array('limit' => 10))
                ->addColumn('entregable_type', 'string', array('limit' => 100))
                ->addColumn('cliente_id', 'integer', array('limit' => 10))
                ->addColumn('centro_facturacion_id', 'integer', array('limit' => 10))
                ->addColumn('estado_id', 'integer', array('limit' => 10))
                
                ->addColumn('saldo_facturar', 'decimal', array('scale' => 2, 'precision' => 10))
                
                ->addColumn('created_by', 'integer', array('limit' => 10))
                ->addColumn('empresa_id', 'integer', array('limit' => 10))
                ->addColumn('created_at', 'datetime')
                ->addColumn('updated_at', 'datetime')
                ->save();
        
        $table2 = $this->table('entalq_entregas_alquiler_catalogos');
        $table2
                ->addColumn('nombre', 'string', array('limit' => 100))
                ->addColumn('valor', 'string', array('limit' => 100))
                ->addColumn('tipo', 'string', array('limit' => 100))
                ->save();
        
        $rows = [
            ['id'  => '1', 'nombre'  => 'Por aprobar', 'valor' => 'por_aprobar', 'tipo' => 'estado'],
            ['id'  => '2', 'nombre'  => 'Por entregar', 'valor' => 'por_entregar', 'tipo' => 'estado'],
            ['id'  => '3', 'nombre'  => 'Anulado', 'valor' => 'anulado', 'tipo' => 'estado'],
            ['id'  => '4', 'nombre'  => 'Entregado', 'valor' => 'entregado', 'tipo' => 'estado']
        ];

        $this->insert('entalq_entregas_alquiler_catalogos', $rows);
        
        $rows2 = [
            ['id'  => '5', 'nombre'  => 'Por hora', 'valor' => 'por_hora', 'tipo' => 'tarifa'],
            ['id'  => '6', 'nombre'  => 'Diario', 'valor' => 'diario', 'tipo' => 'tarifa'],
            ['id'  => '7', 'nombre'  => 'Semanal', 'valor' => 'semanal', 'tipo' => 'tarifa'],
            ['id'  => '8', 'nombre'  => 'Mensual', 'valor' => 'mensual', 'tipo' => 'tarifa'],
        ];

        $this->insert('conalq_contratos_alquiler_catalogos', $rows2);
    }

    public function down()
    {
        $this->dropTable('entalq_entregas_alquiler');
        $this->dropTable('entalq_entregas_alquiler_catalogos');
    }
}
