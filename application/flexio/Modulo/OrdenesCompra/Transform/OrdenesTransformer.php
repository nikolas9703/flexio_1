<?php
namespace Flexio\Modulo\OrdenesCompra\Transform;
use Flexio\Modulo\OrdenesCompra\Models\OrdenesCompra;
class OrdenesTransformer{
  public function crearInstancia($linesItems){
    $model=[];
    foreach($linesItems as $item){
      if(isset($item['id'])){
        array_push($model,$this->setData($item));
      }else{
        array_push($model,new OrdenesCompra($item));
      }
    }
    return $model;
  }
  function setData($item){
    $line = OrdenesCompra::find($item['id']);
    foreach($item as $key => $value){
      if($key !='id' )$line->{$key} = $value;
    }
    return $line;
  }

}
