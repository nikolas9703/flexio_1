<?php
namespace Flexio\Modulo\Bodegas\Repository;

use Flexio\Modulo\Bodegas\Models\Bodegas as Bodegas;

class BodegasCambiarEstadoRepository implements BodegasCambiarEstadoInterface{
    protected $bodegas;
    protected $bodega;
    
    protected static $contieneEntradas = [];

    public function cambiarEstado($bodega_id, $estadoRef){
        $estado = $estadoRef == "1" ? "2" : "1";
        $bodega = Bodegas::find($bodega_id);
        self::$contieneEntradas = [];
        $this->recursivaTiene($bodega, $estado);
        if(in_array(true, self::$contieneEntradas)){
            return $response= array('estado'=>500,'mensaje' => '<b>¡Error!</b> La bodega tiene entradas asociadas');
        }
        $this->bodegaEstado($bodega, $estado);
        return $response= array('estado'=>200,'mensaje' => '<b>¡&Eacute;xito!</b> La actualizaci&oacute;n de estado');
    }

    private static function recursivaTiene(Bodegas $bodega, $estado)
    {
        $aux = $bodega->where(function($q) use ($bodega){
                    $q->where("id", $bodega->id);
                    $q->has("ajustes", "=", "0");
                    $q->has("traslados", "=", "0");
                    $q->has("ordenes_compras", "=", "0");
                })->count() > 0 ? true : false;//true si no tiene entradas / false si tiene entradas
        
        if(!$aux && $estado!='1'){
            array_push(self::$contieneEntradas,true);
        }
        if($bodega->bodega_hijos->count() > 0){
            $bodega->bodega_hijos->map(function($ele) use($estado){
                self::recursivaTiene($ele, $estado);
          });
        }
        return self::$contieneEntradas;
    }

    private function bodegaEstado($bodega, $estado){
        if($estado =="1"){
            self::updatePadreEstado($bodega, $estado);
        }
        self::updateHijoEstado($bodega, $estado);
    }

    private static function updatePadreEstado($bodegaRef, $estado){
        $bodegaRef->estado = $estado;
        $bodegaRef->save();
        $bodega = Bodegas::where('id',$bodegaRef->padre_id)->get()->last();
        if(!is_null($bodega)){
            self::updatePadreEstado($bodega, $estado);
        }
    }

    private static function updateHijoEstado($bodegaRef, $estado){
        $bodegaRef->estado = $estado;
        $bodegaRef->save();

        if($bodegaRef->bodega_hijos->count() > 0){
            $bodegaRef->bodega_hijos->map(function($ele) use($estado){
                self::updateHijoEstado($ele, $estado);
            });
        }
    }
}
