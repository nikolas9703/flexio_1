<?php

use \Flexio\Migration\Migration;

class ContratoAlquilerCatalogoV4 extends Migration
{
    public function up() {

      //insertar nuevos valores
      $rows = [
          ['nombre'  => '6 d&iacute;as', 'valor' => '6_dias', 'tipo' => 'tarifa'],
      ];
      $this->insert('conalq_contratos_alquiler_catalogos', $rows);

      //ordenar catalogo
      $this->execute('UPDATE conalq_contratos_alquiler_catalogos SET orden = 4 WHERE valor = "6_dias" AND tipo="tarifa"');
    }

    public function down(){
      $this->execute('DELETE FROM conalq_contratos_alquiler_catalogos WHERE valor = "6_dias" AND tipo="tarifa"');
    }
}
