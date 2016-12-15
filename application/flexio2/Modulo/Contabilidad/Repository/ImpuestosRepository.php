<?php
namespace Flexio\Modulo\Contabilidad\Repository;

use Flexio\Modulo\Contabilidad\Models\Impuestos as Impuestos;

class ImpuestosRepository implements ImpuestosInterface{
    public function find($impuesto_id) {
        return Impuestos::find($impuesto_id);
    }
    public function findByUuid($uuid) {
        return Impuestos::where("uuid_impuesto", hex2bin($uuid))->first();
    }
    
    public function get($clause = []){
        
        $impuestos = Impuestos::where(function($query) use ($clause){
            
            $this->_filtros($query, $clause);
            
        });
        return $impuestos->get();
        
    }
    
    private function _filtros($query, $clause){
        
        if(isset($clause['empresa_id']) and !empty($clause['empresa_id'])){$query->whereEmpresaId($clause['empresa_id']);}
        
    }
    
}
