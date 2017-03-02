<?php

use \Flexio\Migration\Migration;

class NuevoMetodoPagoCatalogo extends Migration
{
  public function up()
  {
      $rows = [
        ['key' => 13, 'valor' => 'Transferencia internacional', 'etiqueta' => 'transferencia_internacional', 'tipo' => 'metodo_pago', 'modulo' => 'pagos', 'orden' => 11],
      ];

      $this->insert('flexio_catalogos', $rows);
  }

  public function down()
  {
      $this->execute("DELETE FROM flexio_catalogos WHERE key IN (13) and modulo = 'pagos'");
  }
}
