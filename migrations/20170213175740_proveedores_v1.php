<?php

use \Flexio\Migration\Migration;

class ProveedoresV1 extends Migration
{
    public function up()
    {
        $rows = [
            ['id_cat' => 22, 'id_campo' => 29, 'valor' => 'pago_inmediato', 'etiqueta' => 'Pago inmediato', 'orden' => 0]
        ];

        $this->insert('pro_proveedores_cat', $rows);
    }

    public function down()
    {
        $this->execute('DELETE FROM pro_proveedores_cat WHERE id_cat = 22');
    }
}
