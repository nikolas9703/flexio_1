<?php
namespace Flexio\Modulo\ReporteFinanciero\Reportes\GananciaPerdida\Csv;
use Flexio\Modulo\ReporteFinanciero\Reportes\BalanceSituacion\Csv\Csv;

class Ingreso extends Csv{

    function csv_footer($activo, $csv){

        $new_footer = $this->crear_footer($activo);
        $ganancias = $this->ganancia_bruta($activo);
        $csv->insertOne($new_footer);
        $csv->insertOne($ganancias);
        return $this;
    }

  protected function crear_footer($data){
        $footer =  (array)$data[0];
        $new_footer = array_values(array_diff_key( $footer, array_flip(['id','nombre','padre_id','codigo'])));
        array_unshift($new_footer, "Total de ingreso");
        return $new_footer;
    }

  protected function ganancia_bruta($data){
    $footer =  (array)$data[0];
    $new_footer = array_values(array_diff_key( $footer, array_flip(['id','nombre','padre_id','codigo'])));
    array_unshift($new_footer, "Ganancia bruta");
    return $new_footer;
  }
}
