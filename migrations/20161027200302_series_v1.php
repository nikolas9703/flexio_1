<?php

use \Flexio\Migration\Migration;

class SeriesV1 extends Migration
{
    public function up()
    {
        $rows = [
            ['key' => '1', 'valor' => 'No disponible', 'etiqueta' => 'no_disponible', 'tipo' => 'estado', 'modulo' => 'series', 'orden' => '2'],
            ['key' => '2', 'valor' => 'Disponible', 'etiqueta' => 'disponible', 'tipo' => 'estado', 'modulo' => 'series', 'orden' => '4']
        ];

        $this->insert('flexio_catalogos', $rows);
    }

    public function down()
    {
        $this->execute("DELETE FROM flexio_catalogos WHERE modulo = 'series' and `key` in (1,2)");
    }
}
