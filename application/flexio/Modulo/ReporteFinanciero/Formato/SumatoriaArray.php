<?php
namespace Flexio\Modulo\ReporteFinanciero\Formato;
use Flexio\Modulo\ReporteFinanciero\Models\ReporteCuentas;

class SumatoriaArray{
  protected static $listaArray = [];
  protected static $level = 0;

  function sumarColumna($cuentas,$empresa_id, $tipo_cuenta){
    self::$listaArray = [];
    self::$listaArray = $cuentas;
    $cuentasHijos = $this->cuentas_hijos($empresa_id,$tipo_cuenta);

    foreach($cuentasHijos as $cuenta){
      $this->sumatoria($cuenta);
    }
    return self::$listaArray;
  }

  function sumatoria($cuenta){
    $filter = self::$listaArray->where('id',$cuenta->id)->first();

    for($i = 0;$i<count(self::$listaArray);$i++){

      if(self::$listaArray[$i]->id == $filter->padre_id){
        foreach(self::$listaArray[$i] as $key=> $columna){
          if(!in_array($key,['id','nombre','codigo','padre_id'])){
            self::$listaArray[$i]->{$key} = self::$listaArray->where('padre_id',self::$listaArray[$i]->id)->sum($key);
          }
        }

        $this->sumatoria(self::$listaArray[$i]);
      }
    }
  }

  function cuentas_hijos($empresa_id,$tipo_cuenta){
    $builder =  (new ReporteCuentas)->newQuery();
    return $builder->where(function($query)use($empresa_id,$tipo_cuenta){
        $query->where("empresa_id",$empresa_id)
              ->where('tipo_cuenta_id',$tipo_cuenta)
              ->whereRaw('id NOT IN (padre_id)');
    })
    ->get(['id','padre_id']);
    //return Cuentas::deTipo($tipo_cuenta)->transaccionalesDeEmpresa($empresa_id)->toSql();
  }

}
