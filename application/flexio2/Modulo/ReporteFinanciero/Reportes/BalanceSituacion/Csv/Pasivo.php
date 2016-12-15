<?php
namespace Flexio\Modulo\ReporteFinanciero\Reportes\BalanceSituacion\Csv;

class Pasivo extends Csv{

    function csv_footer($data, $csv){

        $new_footer = $this->crear_footer($data);
        $csv->insertOne($new_footer);
        return $this;
    }

  protected function crear_footer($data){
        $footer =  (array)$data[0];
        $new_footer = array_values(array_diff_key( $footer, array_flip(['id','nombre','padre_id','codigo'])));
        array_unshift($new_footer, "Total de Pasivo");
        return $new_footer;
    }
}
