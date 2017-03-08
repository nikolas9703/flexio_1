<?php
namespace Flexio\Modulo\OrdenesCompra\Transform;
use Flexio\Modulo\Pedidos\Models\Pedidos;
class PedidosTransformer{
  public function crearInstancia($linesItems){
    $model=[];
    foreach($linesItems as $item){
      if(isset($item['id'])){
        array_push($model,$this->setData($item));
      }else{
        array_push($model,new Pedidos($item));
      }
    }
    return $model;
  }
  function setData($item){
    $line = Pedidos::find($item['id']);
    foreach($item as $key => $value){
      if($key !='id' )$line->{$key} = $value;
    }
    return $line;
  }

}
