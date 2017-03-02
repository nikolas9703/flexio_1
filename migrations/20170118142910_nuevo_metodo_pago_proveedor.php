<?php

use \Flexio\Migration\Migration;

class NuevoMetodoPagoProveedor extends Migration
{
  public function up()
  {
      $rows = [
          [
              'key'    => 20,
              'valor'     => 'Transferencia Internacional',
              'etiqueta'  => 'transferencia_internacional',
              'tipo'  => 'pago',
              'orden'  => 7
          ],

      ];
      $this->insert('cob_cobro_catalogo', $rows);
  }

  public function down()
  {
      $this->execute("DELETE FROM cob_cobro_catalogo WHERE key IN (20) and tipo = 'pago'");
  }
}
