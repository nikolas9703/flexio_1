<?php
namespace Flexio\Modulo\Cliente\SaveMany;
use Flexio\Modulo\Cliente\Models\Telefonos;

class TelefonosTransform{

    public function crearInstancia($linesTelefonos) {
        $model=[];
        foreach($linesTelefonos as $item){
            if(isset($item['id'])){
                array_push($model,$this->BuscarExistente($item));
            }else{
                array_push($model,new Telefonos($item));
            }
        }
        return $model;
    }

    function BuscarExistente($item) {
        $line = Telefonos::find($item['id']);
        foreach($item as $key => $value){
            if($key !='id' )$line->{$key} = $value;
        }
        return $line;
    }

}
