<?php
namespace Flexio\Modulo\Cliente\SaveMany;
use Flexio\Modulo\Cliente\Models\Asignados;

class AsignadosTransform{

  public function crearInstancia($linesAsignados) {
    $model=[];
    foreach($linesAsignados as $item){
      if(isset($item['id'])){
        array_push($model,$this->BuscarExistente($item));
      }else{
        array_push($model,new Asignados($item));
      }
    }
    return $model;
  }

  function BuscarExistente($item) {
    $line = Asignados::find($item['id']);
    foreach($item as $key => $value){
      if($key !='id' )$line->{$key} = $value;
    }
    return $line;
  }

}
