<?php

use \Flexio\Migration\Migration;

class SubcontratosV4 extends Migration
{
    public function up()
    {
        $rows = [
            ['key' => '1', 'valor'  => 'Por aprobar', 'etiqueta' => 'por_aprobar', 'tipo' => 'estado', 'modulo' => 'subcontratos', 'orden' => '2'],
            ['key' => '2', 'valor'  => 'Vigente', 'etiqueta' => 'vigente', 'tipo' => 'estado', 'modulo' => 'subcontratos', 'orden' => '4'],
            ['key' => '3', 'valor'  => 'Terminado', 'etiqueta' => 'terminado', 'tipo' => 'estado', 'modulo' => 'subcontratos', 'orden' => '6'],
            ['key' => '4', 'valor'  => 'Anulado', 'etiqueta' => 'anulado', 'tipo' => 'estado', 'modulo' => 'subcontratos', 'orden' => '8']
        ];

        $this->insert('flexio_catalogos', $rows);
    }

    public function down()
    {
        $this->execute("DELETE FROM flexio_catalogos WHERE modulo = 'subcontratos' and `key` in (1,2,3,4)");
    }
}
