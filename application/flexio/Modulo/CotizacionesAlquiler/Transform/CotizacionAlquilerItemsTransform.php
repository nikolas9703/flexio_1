<?php
namespace Flexio\Modulo\CotizacionesAlquiler\Transform;
use Flexio\Modulo\CotizacionesAlquiler\Models\CotizacionesAlquilerItems;

class CotizacionAlquilerItemsTransform {

  public function crearInstancia($linesItems){
    $model=[];
    foreach($linesItems as $item){
        $item['precio_unidad'] = str_replace(",","",  $item['precio_unidad']);
      unset($item['item_hidden']);
      if(isset($item['id'])){
      array_push($model,$this->setData($item));
    }else{
      array_push($model,new CotizacionesAlquilerItems($item));
    }
    }
    return $model;
  }

  function setData($item){
    $line = CotizacionesAlquilerItems::find($item['id']);
    foreach($item as $key => $value){

      if($key !='id' )$line->{$key} = $value;
    }
    return $line;
  }

}
