<?php
namespace Flexio\Modulo\Documentos\Transform;
use Flexio\Modulo\Documentos\Models\Documentos as Documentos;
class DocumentosTransformer{

  public function crearInstancia($linesItems){
    $model=[];
    foreach($linesItems as $item){
      if(isset($item['id'])){
      array_push($model,$this->setData($item));
    }else{
      array_push($model,new Documentos($item));
    }
    }
    return $model;
  }

  function setData($item){
    $line = Documentos::find($item['id']);
    foreach($item as $key => $value){

      if($key !='id' )$line->{$key} = $value;
    }
    return $line;
  }

}
