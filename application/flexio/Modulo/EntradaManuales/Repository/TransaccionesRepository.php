<?php
namespace Flexio\Modulo\EntradaManuales\Repository;

use Flexio\Modulo\EntradaManuales\Models\AsientoContable as Transacciones;

class TransaccionesRepository{
    
    public function get($clause = array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
    {
        $transacciones = Transacciones::deEmpresa($clause["empresa_id"]);
        
        $this->_filtros($transacciones, $clause);
        
        if($sidx!=NULL && $sord!=NULL){$transacciones->orderBy($sidx, $sord);}
        if($limit!=NULL){$transacciones->skip($start)->take($limit);}
        
        return $transacciones->get();
    }
    
    public function count($clause = array())
    {
        $transacciones = Transacciones::deEmpresa($clause["empresa_id"]);
        
        $this->_filtros($transacciones, $clause);
        
        return $transacciones->count();
    }
    
    private function _filtros($transacciones, $clause)
    {
        if(isset($clause["cuenta_id"]) and !empty($clause["cuenta_id"])){$transacciones->deCuenta($clause["cuenta_id"]);}
        if(isset($clause["fecha_inicio"]) and !empty($clause["fecha_inicio"])){$transacciones->deFechaInicio($clause["fecha_inicio"]);}
        if(isset($clause["fecha_fin"]) and !empty($clause["fecha_fin"])){$transacciones->deFechaFin($clause["fecha_fin"]);}
        if(isset($clause["no_conciliados"]) and $clause["no_conciliados"]){$transacciones->noConciliados();}
    }
    
    public function getCollectionTransacciones($trasacciones)
    {
        $aux = [];
        
        foreach($trasacciones as $transaccion)
        {
            $aux[] = [
                "id"                    => $transaccion->id,
                "numero"                => $transaccion->codigo,
                "fecha"                 => $transaccion->created_at,
                "transaccion"           => $transaccion->nombre,
                "monto"                 => $transaccion->monto,
                "color"                 => $transaccion->color,
                "balance_verificado"    => [
                    "monto"     => 0,//es el monto al momento de marcar el checkbox
                    "checked"   => false,
                    "order"     => 0
                ]
            ];
        }
        
        return $aux;
    }
    
    
    
//    public function find($item_id){
//        return Items::find($item_id);
//    }
//    public function findByUuid($uuid_item) {
//        return Items::where("uuid_item", hex2bin($uuid_item))->first();
//    }
}
