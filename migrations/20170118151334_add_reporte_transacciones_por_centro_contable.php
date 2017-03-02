<?php

use \Flexio\Migration\Migration;

class AddReporteTransaccionesPorCentroContable extends Migration
{
  public function up()
  {
      // inserting multiple rows
      $rows = [
          [
            'tipo'    => 'reporte',
            'etiqueta' => 'transacciones_por_centro_contable',
            'valor' => 'Transacciones contables por centro contable',
            'orden'  => '13'
          ]
      ];
      $this->insert('cat_reporte_financiero', $rows);
  }

  public function down()
  {
      $this->execute('DELETE FROM cat_reporte_financiero WHERE etiqueta IN("transacciones_por_centrocontable")');
  }
}
