<?php

use \Flexio\Migration\Migration;

class AddReporteCompras extends Migration
{
  public function up()
  {
      // inserting multiple rows
      $rows = [
          [
            'tipo'    => 'reporte',
            'etiqueta' => 'costo_por_centro_compras',
            'valor' => 'Reporte de compras',
            'orden'  => '11'
          ]
      ];
      $this->insert('cat_reporte_financiero', $rows);
  }

  public function down()
  {
      $this->execute('DELETE FROM cat_reporte_financiero WHERE etiqueta IN("costo_por_centro_compras")');
  }
}
