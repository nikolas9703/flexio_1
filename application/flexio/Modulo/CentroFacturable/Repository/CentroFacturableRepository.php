<?php

namespace Flexio\Modulo\CentroFacturable\Repository;
use Flexio\Modulo\CentroFacturable\Models\CentroFacturable;


class CentroFacturableRepository{

  function find($id){
    return  CentroFacturable::findOrFail($id);
  }
  
    private function _filtros($query, $clause){
        
        if(isset($clause['empresa_id']) and !empty($clause['empresa_id'])){$query->whereEmpresaId($clause['empresa_id']);}
        
    }
  
    public function get($clause){
        
        $centros_facturables = CentroFacturable::where(function($query) use ($clause){
            
            $this->_filtros($query, $clause);
            
        });
        
        return $centros_facturables->get();
        
    }

}
