<?php

use \Flexio\Migration\Migration;

class PagosV2 extends Migration
{

    public function up()
    {
        $rows = [
            ['key' => 1, 'valor' => 'Pagar de cuenta de banco', 'etiqueta' => 'banco', 'tipo' => 'tipo_pago', 'modulo' => 'pagos', 'orden' => 1],
            ['key' => 2, 'valor' => 'Pagar de caja', 'etiqueta' => 'caja', 'tipo' => 'tipo_pago', 'modulo' => 'pagos', 'orden' => 2],
        ];

        $this->insert('flexio_catalogos', $rows);
    }

    public function down()
    {
        $this->execute("DELETE FROM flexio_catalogos WHERE key IN (1, 2) and modulo = 'pagos'");
    }

}
