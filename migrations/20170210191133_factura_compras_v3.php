<?php

use \Flexio\Migration\Migration;

class FacturaComprasV3 extends Migration
{
    public function up()
    {
        // inserting multiple rows
        $rows = [
            [
                'key' => '21',
                'valor' => 'Pago inmediato',
                'etiqueta' => 'pago_inmediato',
                'tipo' => 'termino_pago',
                'orden' => 0
            ]
        ];

        // this is a handy shortcut
        $this->insert('fac_factura_catalogo', $rows);
    }

    public function down()
    {
        $this->execute('DELETE FROM fac_factura_catalogo `key` = 21');
    }
}
