<?php

use \Flexio\Migration\Migration;

class ContratoAlquilerCatalogoV2 extends Migration
{
    public function up() {

      //insertar nuevos valores
      $rows = [
          ['nombre'  => '15 d&iacute;as', 'valor' => '15_dias', 'tipo' => 'tarifa'],
          ['nombre'  => '28 d&iacute;as', 'valor' => '28_dias', 'tipo' => 'tarifa'],
          ['nombre'  => '30 d&iacute;as', 'valor' => '30_dias', 'tipo' => 'tarifa'],
      ];
      $this->insert('conalq_contratos_alquiler_catalogos', $rows);

      //ordenar catalogo
      $this->execute('UPDATE conalq_contratos_alquiler_catalogos SET orden = 4 WHERE valor = "15_dias" AND tipo="tarifa"');
      $this->execute('UPDATE conalq_contratos_alquiler_catalogos SET orden = 5 WHERE valor = "28_dias" AND tipo="tarifa"');
      $this->execute('UPDATE conalq_contratos_alquiler_catalogos SET orden = 6 WHERE valor = "30_dias" AND tipo="tarifa"');
    }

    public function down(){
      $this->execute('DELETE FROM conalq_contratos_alquiler_catalogos WHERE valor = "15_dias" AND tipo="tarifa"');
      $this->execute('DELETE FROM conalq_contratos_alquiler_catalogos WHERE valor = "28_dias" AND tipo="tarifa"');
      $this->execute('DELETE FROM conalq_contratos_alquiler_catalogos WHERE valor = "30_dias" AND tipo="tarifa"');
    }
}
