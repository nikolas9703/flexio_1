<?php
namespace Flexio\Modulo\Cliente\SaveMany;
use Flexio\Modulo\Cliente\Models\Correos;

class CorreosTransform{

    public function crearInstancia($linesCorreos) {
        $model=[];
        foreach($linesCorreos as $item){
            if(isset($item['id'])){
                array_push($model,$this->BuscarExistente($item));
            }else{
                array_push($model,new Correos($item));
            }
        }
        return $model;
    }

    function BuscarExistente($item) {
        $line = Correos::find($item['id']);
        foreach($item as $key => $value){
            if($key !='id' )$line->{$key} = $value;
        }
        return $line;
    }

}
