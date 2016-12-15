<?php

use \Flexio\Migration\Migration;

class PagosV3 extends Migration
{

    public function up()
    {
        $rows = [
            ['key' => 3, 'valor' => 'por_aprobar', 'etiqueta' => 'Por aprobar', 'tipo' => 'etapa', 'modulo' => 'pagos', 'orden' => 2],
            ['key' => 4, 'valor' => 'por_aplicar', 'etiqueta' => 'Por aplicar', 'tipo' => 'etapa', 'modulo' => 'pagos', 'orden' => 4],
            ['key' => 5, 'valor' => 'aplicado', 'etiqueta' => 'Aplicado', 'tipo' => 'etapa', 'modulo' => 'pagos', 'orden' => 6],
            ['key' => 6, 'valor' => 'anulado', 'etiqueta' => 'Anulado', 'tipo' => 'etapa', 'modulo' => 'pagos', 'orden' => 8],
            ['key' => 7, 'valor' => 'cheque_en_transito', 'etiqueta' => 'Cheque en transito', 'tipo' => 'etapa', 'modulo' => 'pagos', 'orden' => 10],
            ['key' => 8, 'valor' => 'efectivo', 'etiqueta' => 'Efectivo', 'tipo' => 'metodo_pago', 'modulo' => 'pagos', 'orden' => 2],
            ['key' => 9, 'valor' => 'credito_favor', 'etiqueta' => 'Cr&eacute;dito a favor', 'tipo' => 'metodo_pago', 'modulo' => 'pagos', 'orden' => 4],
            ['key' => 10, 'valor' => 'cheque', 'etiqueta' => 'Cheque', 'tipo' => 'metodo_pago', 'modulo' => 'pagos', 'orden' => 6],
            ['key' => 11, 'valor' => 'tarjeta_credito', 'etiqueta' => 'Tarjeta de cr&eacute;dito', 'tipo' => 'metodo_pago', 'modulo' => 'pagos', 'orden' => 8],
            ['key' => 12, 'valor' => 'ach', 'etiqueta' => 'ACH', 'tipo' => 'metodo_pago', 'modulo' => 'pagos', 'orden' => 10],
        ];

        $this->insert('flexio_catalogos', $rows);
    }

    public function down()
    {
        $this->execute("DELETE FROM flexio_catalogos WHERE key IN (3, 4, 5, 6, 7, 8, 9, 10, 11, 12) and modulo = 'pagos'");
    }

}
