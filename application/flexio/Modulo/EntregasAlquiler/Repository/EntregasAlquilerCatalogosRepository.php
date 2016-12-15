<?php
namespace Flexio\Modulo\EntregasAlquiler\Repository;

use Flexio\Modulo\EntregasAlquiler\Models\EntregasAlquilerCatalogos;

class EntregasAlquilerCatalogosRepository
{
    
    private function _filtros($query, $clause)
    {
        if(isset($clause['tipo']) and !empty($clause['tipo'])){$query->whereTipo($clause['tipo']);}
    }
    
    public function get($clause = array(), $sidx = null, $sord = null, $limit = null, $start = null)
    {
        $catalogos = EntregasAlquilerCatalogos::where(function($query) use ($clause){
            
            $this->_filtros($query, $clause);
            
        });
        
        if($sidx !== null && $sord !== null){$catalogos->orderBy($sidx, $sord);}
        if($limit != null){$catalogos->skip($start)->take($limit);}
        return $catalogos->get();
    }
    
    
    
}
