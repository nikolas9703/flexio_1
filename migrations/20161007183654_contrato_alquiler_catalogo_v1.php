<?php

use \Flexio\Migration\Migration;

class ContratoAlquilerCatalogoV1 extends Migration
{
    public function up() {

      //Agregar columna orden si no existe
      $table = $this->table('conalq_contratos_alquiler_catalogos');
      $column = $table->hasColumn('orden');
      if (!$column) {
        $table->addColumn('orden', 'integer', array('limit' => 10, 'after' => 'tipo'))->save();
      }

      //insertar nuevo valor
      $rows = [
          ['nombre'  => '4 horas', 'valor' => '4_horas', 'tipo' => 'tarifa']
      ];
      $this->insert('conalq_contratos_alquiler_catalogos', $rows);

      //ordenar catalogo
      $this->execute('UPDATE conalq_contratos_alquiler_catalogos SET orden = 1 WHERE valor = "por_hora" AND tipo="tarifa"');
      $this->execute('UPDATE conalq_contratos_alquiler_catalogos SET orden = 2 WHERE valor = "4_horas" AND tipo="tarifa"');
      $this->execute('UPDATE conalq_contratos_alquiler_catalogos SET orden = 3 WHERE valor = "diario" AND tipo="tarifa"');
      $this->execute('UPDATE conalq_contratos_alquiler_catalogos SET orden = 7 WHERE valor = "semanal" AND tipo="tarifa"');
      $this->execute('UPDATE conalq_contratos_alquiler_catalogos SET orden = 8 WHERE valor = "mensual" AND tipo="tarifa"');
    }

    public function down(){
      $this->execute('DELETE FROM conalq_contratos_alquiler_catalogos WHERE valor = "4_horas" AND tipo="tarifa"');
    }
}
