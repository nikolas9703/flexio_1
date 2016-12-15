<?php
namespace Flexio\Modulo\ReporteFinanciero\Reportes\BalanceSituacion\Csv;

abstract class Csv{
    public function crear($datos, $csv){
        return $this
         ->csv_header($datos, $csv)
         ->csv_body($datos,$csv)
         ->csv_footer($datos, $csv);
    }

    protected function csv_header($activo, $csv){
        $header =  (array)$activo[0];
        $header_csv = array_keys($header);
        $new_header = array_values( array_filter($header_csv,function($cabecera){
          if(!in_array($cabecera,['id','nombre','padre_id','codigo'])){
            return $cabecera;
          }
        }));
        array_unshift($new_header, "");
        $csv->insertOne($new_header);
        return $this;
    }

   protected  function csv_body($activo, $csv){
        $i = 0;
        $data_activo = [];
        foreach ($activo as $cuenta_activo){

            $data_activo[$i]["cuenta"] =  utf8_decode($cuenta_activo->codigo ." ".$cuenta_activo->nombre);
            foreach($cuenta_activo as $key=>$data){
                if(!in_array($key,['id','nombre','padre_id','codigo'])){
                    $data_activo[$i][$key] = $cuenta_activo->$key;
                }
            }
            $i++;
        }
        $csv->insertAll($data_activo);
        return $this;
    }
    abstract function csv_footer($datos, $csv);
}
