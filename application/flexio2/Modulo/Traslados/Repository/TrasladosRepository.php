<?php
namespace Flexio\Modulo\Traslados\Repository;

//utilities
use Illuminate\Database\Capsule\Manager as Capsule;

//repositorios
use Flexio\Modulo\Bodegas\Repository\BodegasRepository as bodegasRep;
use Flexio\Modulo\Inventarios\Repository\ItemsRepository as itemsRep;
use Flexio\Modulo\Entradas\Repository\EntradasRepository as entradasRep;
use Flexio\Modulo\Salidas\Repository\SalidasRepository as salidasRep;
use Flexio\Modulo\Inventarios\Repository\UnidadesRepository as unidadesRep;

//modelos
use Flexio\Modulo\Traslados\Models\Traslados as Traslados;

class TrasladosRepository implements TrasladosInterface{
    
    private $bodegasRep;
    private $itemsRep;
    private $entradasRep;
    private $salidasRep;
    private $unidadesRep;
    
    //variables del entorno
    private $prefijo = "TRAS";
    
    public function __construct() {
        $this->bodegasRep   = new bodegasRep();
        $this->itemsRep     = new itemsRep();
        $this->entradasRep  = new entradasRep();
        $this->salidasRep   = new salidasRep();
        $this->unidadesRep  = new unidadesRep();
    }

    public function findByUuid($uuid){
        return Traslados::where('uuid_traslado',hex2bin($uuid))->first();
    }
    
    private function _create($traslado, $campo)
    {
        $traslado->uuid_traslado        = Capsule::raw("ORDER_UUID(uuid())");
        $traslado->referencia           = "Traslado";
        $traslado->numero               = str_replace($this->prefijo, "", $campo["numero"]);
        $traslado->uuid_lugar           = hex2bin(strtolower($campo["a_bodega"]));
        $traslado->uuid_lugar_anterior  = hex2bin(strtolower($campo["de_bodega"]));
        $traslado->credito              = 0;
        $traslado->dias                 = 0;
        $traslado->id_estado            = 1;//Por enviar
        $traslado->creado_por           = $campo["usuario_id"];
        $traslado->fecha_creacion       = date("Y-m-d", time());
        $traslado->fecha_entrega        = date("Y-m-d", strtotime($campo["fecha_entrega"]));
        $traslado->id_empresa           = $campo["empresa_id"];
        $traslado->monto                = 0;
        $traslado->uuid_pedido          = hex2bin(strtolower($campo["pedido"]));
        
        $traslado->save();
    }
    
    private function _syncItems($traslado, $items)
    {
        
        $traslado->traslados_items()->whereNotIn('id',array_pluck($items,'id_traslado_item'))->delete();
        foreach ($items as $ti) {
            
            $item       = $this->itemsRep->findByUuid($ti["item"]);
            $unidad_id  = is_numeric($ti["unidad"]) ? $ti["unidad"] : $this->unidadesRep->findByUuid($ti["unidad"])->id;

            $traslado_item_id = (isset($item['id_traslado_item']) and !empty($item['id_traslado_item'])) ? $item['id_traslado_item'] : '';
            $traslado_item = $traslado->traslados_items()->firstOrNew(['id'=>$traslado_item_id]);
            $traslado_item->item_id = $item->id;
            $traslado_item->cantidad = $ti["cantidad_enviada"];
            $traslado_item->unidad_id = $unidad_id;
            $traslado_item->precio_unidad = $ti["precio_unidad"];
            $traslado_item->observacion = $ti["observacion"];
            $traslado_item->descuento = 0;
            $traslado_item->save();
            
        }
        
    }
    
    public function create($params)
    {
        $campo      = $params["campo"];
        $items      = $params["items"];
        $registro   = new Traslados;
        
        $this->_create($registro, $campo);
        $this->_syncItems($registro, $items);
        
        if($this->bodegasRep->find($registro->bodega->id)->raiz->entrada_id == 1)// 1 -> entrada manual : 2 -> entrada automatica 
        {
            $registro->fecha_entrega    = date("Y-m-d", time());
            //CREO UN REGISTRO EN EL MODULO DE SALIDA
            $this->salidasRep->create(array("tipo_id" => $registro->id, "estado_id" => 1, "tipo" => "Flexio\Modulo\Traslados\Models\Traslados", "empresa_id" => $campo["empresa_id"]));
            //CREO UN REGISTRO EN EL MODULO DE ENTRADA
            $this->entradasRep->create(array("tipo_id" => $registro->id, "estado_id" => 1, "tipo" => "Flexio\Modulo\Traslados\Models\Traslados", "empresa_id" => $campo["empresa_id"]));
        }
        
        return $registro;
    }
    
    
}
