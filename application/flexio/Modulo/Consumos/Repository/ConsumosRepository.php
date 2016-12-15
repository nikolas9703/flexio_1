<?php
namespace Flexio\Modulo\Consumos\Repository;

//utilities
use Illuminate\Database\Capsule\Manager as Capsule;

//repositorios
use Flexio\Modulo\Bodegas\Repository\BodegasRepository as bodegasRep;
use Flexio\Modulo\Inventarios\Repository\ItemsRepository as itemsRep;
use Flexio\Modulo\Inventarios\Repository\UnidadesRepository as unidadesRep;
use Flexio\Modulo\Salidas\Repository\SalidasRepository as salidasRep;
use Flexio\Modulo\Contabilidad\Repository\CuentasRepository as cuentasRep;
use Flexio\Modulo\Colaboradores\Repository\ColaboradoresRepository as colaboradoresRep;

//modelos
use Flexio\Modulo\Consumos\Models\Consumos as Consumos;
use Flexio\Modulo\Salidas\Models\Salidas as Salidas;

class ConsumosRepository implements ConsumosInterface{
    
    private $bodegasRep;
    private $itemsRep;
    private $unidadesRep;
    private $salidasRep;
    private $cuentasRep;
    private $colaboradoresRep;
    
    //variables del entorno
    private $prefijo = "CONS";
    
    public function __construct() {
        $this->bodegasRep       = new bodegasRep();
        $this->itemsRep         = new itemsRep();
        $this->unidadesRep      = new unidadesRep();
        $this->salidasRep       = new salidasRep();
        $this->cuentasRep       = new cuentasRep();
        $this->colaboradoresRep = new colaboradoresRep();
    }
    
    public function findByUuid($uudi_consumo) {
        return Consumos::where("uuid_consumo", hex2bin($uudi_consumo))->first();
    }


    public function getColletionCampos($consumo) 
    {
        return [
            "fecha"             => $consumo->created_at,
            "centro_contable"   => $consumo->centro->uuid_centro,
            "bodega_salida"     => $consumo->bodega->uuid_bodega,
            "estado"            => $consumo->estado_id,
            "numero"            => $consumo->numero_documento,
            "colaborador"       => count($consumo->colaborador) ? $consumo->colaborador->id : "",
            "comentarios"       => $consumo->comentarios
        ];
    }
    
    public function getColletionCamposItems($items) 
    {
        $aux = [];
        foreach($items as $item)
        {
            $cuenta = $this->cuentasRep->find($item->pivot->cuenta_id);
            $unidad = $this->unidadesRep->find($item->pivot->unidad_id);
            $aux[] = [
                "categoria"         => $item->pivot->categoria_id,
                "item"              => $item->uuid_item,
                "descripcion"       => $item->descripcion,
                "observacion"       => $item->pivot->observacion,
                "cuenta_gasto"      => $cuenta->uuid_cuenta,
                "cantidad_enviada"  => $item->pivot->cantidad,
                "unidad"            => $unidad->uuid_unidad,
                "id_consumo_item"   => $item->pivot->id
            ];
        }
        return $aux;
    }


    private function _create($post)
    {
        $campo  = $post["campo"];
        
        $registro                       = new Consumos;
        $registro->uuid_consumo         = Capsule::raw("ORDER_UUID(uuid())");
        $registro->referencia           = "Consumo";
        $registro->uuid_centro          = hex2bin(strtolower($campo["centro_contable"]));
        $registro->uuid_bodega          = hex2bin(strtolower($campo["bodega_salida"]));
        $registro->estado_id            = 1;//Por aprobar
        $registro->numero               = $this->genera_numero_consumo($post);
        $registro->uuid_colaborador     = (is_numeric($campo["colaborador"])) ? hex2bin($this->colaboradoresRep->find($campo["colaborador"])->uuid_colaborador) : hex2bin(strtolower($campo["colaborador"]));
        $registro->comentarios          = $campo["comentarios"];

        $registro->created_by           = $post["usuario_id"];
        $registro->empresa_id           = $post["empresa_id"];

        //GUARDO EL REGISTRO
        $registro->save();
        
        return $registro;
    }
    
    private function _save($post)
    {
        $campo  = $post["campo"];
        
        //DATOS GENERALES DEL TRASLADO
        $registro                       = Consumos::where("uuid_consumo", hex2bin($campo["uuid_consumo"]))->first();
        
        $registro->uuid_centro          = hex2bin(strtolower($campo["centro_contable"]));
        $registro->uuid_bodega          = hex2bin(strtolower($campo["bodega_salida"]));
        $registro->estado_id            = $campo["estado"];
        $registro->uuid_colaborador     = (is_numeric($campo["colaborador"])) ? hex2bin($this->colaboradoresRep->find($campo["colaborador"])->uuid_colaborador) : hex2bin(strtolower($campo["colaborador"]));
        $registro->comentarios          = $campo["comentarios"];

        //GUARDO EL REGISTRO
        $registro->save();
        
        return $registro;
    }


    private function _syncItems($registro, $items)
    {
        
        $registro->consumos_items()->whereNotIn('id',array_pluck($items,'id_consumo_item'))->delete();
        foreach ($items as $item)
        {
            
            $aux = (!is_numeric($item["item"])) ? $this->itemsRep->findByUuid($item["item"])->id : $item["item"];
            
            $consumo_item_id = (isset($item['id_consumo_item']) and !empty($item['id_consumo_item'])) ? $item['id_consumo_item'] : '';
            $consumo_item = $registro->consumos_items()->firstOrNew(['id'=>$consumo_item_id]);
            
            $consumo_item->item_id = $aux;
            $consumo_item->categoria_id = $item["categoria"];
            $consumo_item->cantidad = $item["cantidad_enviada"];
            $consumo_item->unidad_id = (!is_numeric($item["unidad"])) ? $this->unidadesRep->findByUuid($item["unidad"])->id : $item["unidad"];
            $consumo_item->cuenta_id = (!is_numeric($item["cuenta_gasto"])) ? $this->cuentasRep->findByUuid($item["cuenta_gasto"])->id : $item["cuenta_gasto"];
            $consumo_item->observacion = $item["observacion"];
            $consumo_item->save();
            
        }
        
        
    }
    
    public function save($post, $fieldset_consumo=NULL, $fieldset_items=NULL)
    {
        //codigo old
        $campo  = $post["campo"];
        $items  = $post["items"];
        
        //instaccioando el registro
        if($fieldset_consumo == NULL)
        {
            $registro = (isset($campo["uuid_consumo"]) and !empty($campo["uuid_consumo"])) ? $this->_save($post) : $this->_create($post);
        }
        else
        {
            $fieldset_consumo["numero"] = $this->genera_numero_consumo($post);

            //Guardar datos desde array $fieldset_consumo
            $registro = Consumos::create($fieldset_consumo);
         }

        //CONSUMOS_ITEMS
        if($fieldset_items == NULL){
                $this->_syncItems($registro, $items);
        }else{

                //Guardar datos desde array $fieldset_items
                foreach($fieldset_items as $item)
                {
                        $item["consumo_id"] = $registro->id;

                        //Guardar datos desde array $fieldset_consumo ->REVISAR PORQUE CAMBIO LA ESTRUCTURA
                        //$consumosItems = Consumos_items_orm::create($item);
                }
        }
        //CREO UN REGISTRO EN EL MODULO DE SALIDA
        //APLICA SOLO PARA LA EDICION CUANDO EL CONSUMO ES APROBADO
        if($campo["estado"] == "1")
        {
            
        }
        if($campo["estado"] == "1" || $campo["estado"] == "2")
        {
            $estadoSalida = ($campo["estado"] == "1") ? "4" : "1";
            if($this->bodegasRep->find($registro->bodega->id)->raiz->entrada_id == 1)// 1 -> entrada manual : 2 -> entrada automatica 
            {
                $this->salidasRep->create(array("tipo_id" => $registro->id, "estado_id" => $estadoSalida, "tipo" => "Flexio\Modulo\Consumos\Models\Consumos", "empresa_id" => $post["empresa_id"]));
            }
        }
        elseif($campo["estado"] == "3")//Consumo anulado
        {
            //borro el registro de la salida...
            Salidas::where("operacion_type", "Flexio\Modulo\Consumos\Models\Consumos")->where("operacion_id", $registro->id)->delete();
        }
        
        return $registro;
    }
    
    private function genera_numero_consumo($post)
    {
    	$countConsumos = Consumos::deEmpresa($post["empresa_id"])->count();

    	return sprintf("%08d", ($countConsumos + 1));
    }
    
}
