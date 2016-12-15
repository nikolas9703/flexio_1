<?php

namespace Flexio\Modulo\Presupuesto\HttpRequest;
use Flexio\Modulo\Presupuesto\Models\CentroCuentaPresupuesto;

class PresupuestoCentro{

  public function crearInstancia($linesItems){
    $model=[];
    foreach($linesItems as $item){
      if(isset($item['id'])){
      array_push($model,$this->setData($item));
    }else{
      array_push($model,new CentroCuentaPresupuesto($item));
    }
    }
    return $model;
  }

  function setData($item){
    $line = CentroCuentaPresupuesto::find($item['id']);
    foreach($item as $key => $value){

      if($key !='id' )$line->{$key} = $value;
    }
    return $line;
  }
}
