<?php namespace Flexio\Modulo\LineItems\Formato;
use Flexio\Modulo\Cotizaciones\Models\LineItem;

class FormatoLineItem{


  public function crearInstancia($linesItems){

    $model=[];
    foreach($linesItems as $item){
      if(isset($item['id']) && !empty($item['id'])){
      array_push($model,$this->setData($item));
    }else{
      array_push($model,new LineItem($item));
    }
    }
    return $model;
  }

   function setData($item){
     $line = LineItem::find($item['id']);
     if(count($line) > 0){
     	foreach($item as $key => $value){
     		if($key !='id' )$line->{$key} = $value;
     	}
     }
     /// se elimina este key del objecto porque no existe en la base de datos.
     //unset($line->lineitem_id);
     return $line;
   }
}
