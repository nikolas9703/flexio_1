<?php
namespace Flexio\Modulo\Bodegas\Repository;

use Flexio\Modulo\Bodegas\Models\Bodegas as Bodegas;

class BodegasRepository{
    protected $bodegas;
    protected $bodega;

    public function find($bodega_id){
        return Bodegas::find($bodega_id);
    }
    public function findByUuid($uuid){
        return Bodegas::where('uuid_bodega',hex2bin($uuid))->first();
    }
    
    function getAll($clause) {
    	return Bodegas::where(function ($query) use($clause) {
    		$query->where('empresa_id', '=', $clause['empresa_id']);
    		if (! empty($clause['estado']))
    			$query->whereIn('estado', $clause['estado']);
    	})->get();
    }
    
    public function get($clause = array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
    {
        $bodegas = Bodegas::deEmpresa($clause["empresa_id"]);
        
        //filtros
        $this->_filtros($bodegas, $clause);
        
        if($sidx!=NULL && $sord!=NULL){$bodegas->orderBy($sidx, $sord);}
        if($limit!=NULL){$bodegas->skip($start)->take($limit);}
        return $bodegas->get();
    }
    
    public function findBy($clause = array())
    {
        $bodega = Bodegas::deEmpresa($clause["empresa_id"]);
        
        //filtros
        $this->_filtros($bodega, $clause);
        
        return $bodega->first();
    }
    
    public function getCollectionBodegas($bodegas){

        return $bodegas->map(function($bodega){
            return [
                'id' => $bodega->uuid_bodega,
                'nombre' => $bodega->nombre,
                'bodega_id' => $bodega->id
            ];
        });
        
    }
    
    private function _filtros($acreedores, $clause)
    {
        if(isset($clause["transaccionales"]) and !empty($clause["transaccionales"])){$acreedores->transaccionales($clause["empresa_id"]);}
        if(isset($clause["uuid_bodega"]) and !empty($clause["uuid_bodega"])){$acreedores->deUuid($clause["uuid_bodega"]);}
    }
}
