<?php
namespace Flexio\Modulo\CentroFacturable\SaveMany;
use Flexio\Modulo\CentroFacturable\Models\CentroFacturable;

class CentroFacturableTransform{

  public function crearInstancia($linesItems){
    $model=[];
    foreach($linesItems as $item){
      if(isset($item['id'])){
        array_push($model,$this->BuscarExistente($item));
      }else{
        array_push($model,new CentroFacturable($item));
      }
    }
    return $model;
  }

  function BuscarExistente($item){
    $line = CentroFacturable::find($item['id']);
    foreach($item as $key => $value){
      if($key !='id' )$line->{$key} = $value;
    }
    return $line;
  }

}
