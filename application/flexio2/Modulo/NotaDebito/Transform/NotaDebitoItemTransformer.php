<?php
namespace Flexio\Modulo\NotaDebito\Transform;
use Flexio\Modulo\NotaDebito\Models\NotaDebitoItem as NotaDebitoItem;
class NotaDebitoItemTransformer{

  public function crearInstancia($linesItems){
    $model=[];
    foreach($linesItems as $item){
      if(isset($item['id'])){
      array_push($model,$this->setData($item));
    }else{
      array_push($model,new NotaDebitoItem($item));
    }
    }
    return $model;
  } 

  function setData($item){
    $line = NotaDebitoItem::find($item['id']);
    foreach($item as $key => $value){

      if($key !='id' )$line->{$key} = $value;
    }
    return $line;
  }

}
