<?php
namespace Flexio\Modulo\NotaCredito\Transform;
use Flexio\Modulo\NotaCredito\Models\NotaCreditoItem as NotaCreditoItem;
class NotaCreditoItemTransformer{

  public function crearInstancia($linesItems){
    $model=[];
    foreach($linesItems as $item){
      if(isset($item['id'])){
      array_push($model,$this->setData($item));
    }else{
      array_push($model,new NotaCreditoItem($item));
    }
    }
    return $model;
  }

  function setData($item){
    $line = NotaCreditoItem::find($item['id']);
    foreach($item as $key => $value){

      if($key !='id' )$line->{$key} = $value;
    }
    return $line;
  }

}
