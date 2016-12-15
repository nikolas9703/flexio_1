<?php
namespace Flexio\Modulo\DepreciacionActivosFijos\Transform;
use Flexio\Modulo\DepreciacionActivosFijos\Models\DepreciacionActivoFijoItem as DepreciacionActivoFijoItem;
class DepreciacionItemTransformer{

  public function crearInstancia($linesItems){
    $model=[];
    foreach($linesItems as $item){
      if(isset($item['id'])){
      array_push($model,$this->setData($item));
    }else{
      array_push($model,new DepreciacionActivoFijoItem($item));
    }
    }
    return $model;
  }

  function setData($item){
    $line = DepreciacionActivoFijoItem::find($item['id']);
    foreach($item as $key => $value){

      if($key !='id' )$line->{$key} = $value;
    }
    return $line;
  }

}
