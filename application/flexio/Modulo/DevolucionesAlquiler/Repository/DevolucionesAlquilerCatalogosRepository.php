<?php
namespace Flexio\Modulo\DevolucionesAlquiler\Repository;

use Flexio\Modulo\DevolucionesAlquiler\Models\DevolucionesAlquilerCatalogos;

class DevolucionesAlquilerCatalogosRepository
{
    
    private function _filtros($query, $clause)
    {
        if(isset($clause['tipo']) and !empty($clause['tipo'])){$query->whereTipo($clause['tipo']);}
    }
    
    public function get($clause = array(), $sidx = null, $sord = null, $limit = null, $start = null)
    {
        $catalogos = DevolucionesAlquilerCatalogos::where(function($query) use ($clause){
            
            $this->_filtros($query, $clause);
            
        });
        
        if($sidx !== null && $sord !== null){$catalogos->orderBy($sidx, $sord);}
        if($limit != null){$catalogos->skip($start)->take($limit);}
        return $catalogos->get();
    }
    
    
    
}
