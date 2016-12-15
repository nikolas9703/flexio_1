<?php
namespace Flexio\Modulo\ReporteFinanciero\Formato;
use Flexio\Modulo\ReporteFinanciero\Models\ReporteCuentas;

class SumarHijosPadres{
  protected static $listaArray = [];
  protected static $level = 0;


  function cambiarKeys($collection){

      foreach ($collection as $i => $prub){
        $collection[$prub->id] = $prub;
        unset($collection[$i]);
      }
    return ['coleccion' =>$collection, 'datos'=>$collection->toArray()];
  }

  function sumarColumnas($cuentas, &$referencia){

      foreach ($cuentas as $indice => $datos) {
          $padre_id = $referencia[$indice]->padre_id;

          $tieneHijos = $cuentas->where('padre_id', $indice)->count();

          if ($tieneHijos == 0 && $padre_id != 0) {
              foreach($referencia[$indice] as $key=> $columna){
                  if(!in_array($key,['id','nombre','codigo','padre_id'])){
                      $referencia[$padre_id]->{$key} += $referencia[$indice]->{$key};
                  }
              }
              unset($cuentas[$indice]);
             $this->sumarColumnas($cuentas, $referencia);
          }
      }

      return $referencia;
  }


}
