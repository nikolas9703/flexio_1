<?php

use \Flexio\Migration\Migration;

class ContratosAlquilerV7 extends Migration
{
    public function up()
    {
        $rows = [
            ['id'  => '1', 'nombre'  => 'Por aprobar', 'valor' => 'por_aprobar', 'tipo' => 'estado'],
            ['id'  => '2', 'nombre'  => 'Vigente', 'valor' => 'vigente', 'tipo' => 'estado'],
            ['id'  => '3', 'nombre'  => 'Anulado', 'valor' => 'anulado', 'tipo' => 'estado'],
            ['id'  => '4', 'nombre'  => 'Terminado', 'valor' => 'terminado', 'tipo' => 'estado'],
        ];

        $this->insert('devalq_devoluciones_alquiler_catalogos', $rows);
    }

    public function down()
    {
        $this->execute('DELETE FROM devalq_devoluciones_alquiler_catalogos WHERE id < 5');
    }
}
