<?php
namespace Flexio\Library\Util;

use Carbon\Carbon as Carbon;

class CatalogoYear{
  protected  $empiezo = 2016;

  public function getEmpiezo() {
    return $this->empiezo;
  }

  public static function get(){
    $empiezo = (new CatalogoYear)->getEmpiezo();
    $array_year = [];

    for($i=0;$i<=6;$i++){
      $fecha =  Carbon::createFromDate($empiezo + $i, 1, 1, 'America/Panama');
      $fechaObj = $fecha->addMonths($i);
      array_push($array_year, array('id'=>$fechaObj->formatLocalized('%Y'),'valor'=> ucfirst($fechaObj->formatLocalized('%Y'))));
    }
    return $array_year;
  }
}
