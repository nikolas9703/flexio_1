<?php
namespace Flexio\Modulo\Inventarios\Repository;

use Flexio\Modulo\Inventarios\Models\Unidades as Unidades;

class UnidadesRepository{
    public function find($unidad_id) {
        
        return Unidades::find($unidad_id);
        
    }
    public function findByUuid($uuid) {
        
        return Unidades::where("uuid_unidad", hex2bin($uuid))->first();
        
    }
    
    public function get($clause = []){
        
        $unidades = Unidades::where(function($query) use ($clause){
            $this->_filtros($query, $clause);
        });
        return $unidades->get();
        
    }
    
    private function _filtros($query, $clause){
        
        if(isset($clause['empresa_id']) and !empty($clause['empresa_id'])){$query->whereEmpresaId($clause['empresa_id']);}
        
    }
    
}
