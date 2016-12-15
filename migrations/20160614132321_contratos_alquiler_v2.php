<?php

use \Flexio\Migration\Migration;

class ContratosAlquilerV2 extends Migration
{
    public function up()
    {
        $table = $this->table('conalq_contratos_alquiler_catalogos');
        $table
                ->addColumn('nombre', 'string', array('limit' => 100))
                ->addColumn('valor', 'string', array('limit' => 100))
                ->addColumn('tipo', 'string', array('limit' => 100))
                ->save();
        
        $rows = [
            ['id'  => '1', 'nombre'  => 'Por aprobar', 'valor' => 'por_aprobar', 'tipo' => 'estado'],
            ['id'  => '2', 'nombre'  => 'Vigente', 'valor' => 'vigente', 'tipo' => 'estado'],
            ['id'  => '3', 'nombre'  => 'Anulado', 'valor' => 'anulado', 'tipo' => 'estado'],
            ['id'  => '4', 'nombre'  => 'Terminado', 'valor' => 'terminado', 'tipo' => 'estado'],
        ];

        $this->insert('conalq_contratos_alquiler_catalogos', $rows);
    }

    public function down()
    {
        $this->dropTable('conalq_contratos_alquiler_catalogos');
    }
}
