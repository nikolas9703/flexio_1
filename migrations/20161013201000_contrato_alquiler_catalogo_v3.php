<?php

use \Flexio\Migration\Migration;

class ContratoAlquilerCatalogoV3 extends Migration
{
    public function up() {

      //insertar nuevos valores
      $rows = [
          ['nombre'  => 'Escalonado', 'valor' => 'escalonado', 'tipo' => 'calculo_costo_retorno'],
          ['nombre'  => 'Proporcional a 30 d&iacute;as', 'valor' => '30_dias', 'tipo' => 'calculo_costo_retorno'],
          ['nombre'  => 'Proporcional a 28 d&iacute;as', 'valor' => '28_dias', 'tipo' => 'calculo_costo_retorno'],
          ['nombre'  => 'Proporcional a 26 d&iacute;as', 'valor' => '26_dias', 'tipo' => 'calculo_costo_retorno'],
          ['nombre'  => 'Periodo completo', 'valor' => 'completo', 'tipo' => 'calculo_costo_retorno'],
      ];
      $this->insert('conalq_contratos_alquiler_catalogos', $rows);

      //ordenar catalogo
      $this->execute('UPDATE conalq_contratos_alquiler_catalogos SET orden = 1 WHERE valor = "escalonado" AND tipo="calculo_costo_retorno"');
      $this->execute('UPDATE conalq_contratos_alquiler_catalogos SET orden = 2 WHERE valor = "30_dias" AND tipo="calculo_costo_retorno"');
      $this->execute('UPDATE conalq_contratos_alquiler_catalogos SET orden = 3 WHERE valor = "28_dias" AND tipo="calculo_costo_retorno"');
      $this->execute('UPDATE conalq_contratos_alquiler_catalogos SET orden = 4 WHERE valor = "26_dias" AND tipo="calculo_costo_retorno"');
      $this->execute('UPDATE conalq_contratos_alquiler_catalogos SET orden = 5 WHERE valor = "completo" AND tipo="calculo_costo_retorno"');
    }

    public function down(){
      $this->execute('DELETE FROM conalq_contratos_alquiler_catalogos WHERE valor = "escalonado" AND tipo="calculo_costo_retorno"');
      $this->execute('DELETE FROM conalq_contratos_alquiler_catalogos WHERE valor = "30_dias" AND tipo="calculo_costo_retorno"');
      $this->execute('DELETE FROM conalq_contratos_alquiler_catalogos WHERE valor = "28_dias" AND tipo="calculo_costo_retorno"');
      $this->execute('DELETE FROM conalq_contratos_alquiler_catalogos WHERE valor = "26_dias" AND tipo="calculo_costo_retorno"');
      $this->execute('DELETE FROM conalq_contratos_alquiler_catalogos WHERE valor = "completo" AND tipo="calculo_costo_retorno"');
    }
}
