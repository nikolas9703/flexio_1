<?php
namespace Flexio\Modulo\Oportunidades\Repository;

use Flexio\Modulo\Oportunidades\Models\OportunidadesCatalogos;

class OportunidadesCatalogosRepository
{
    
    private function _filtros($query, $clause)
    {
        if(isset($clause['tipo']) and !empty($clause['tipo'])){$query->whereTipo($clause['tipo']);}
    }
    
    public function get($clause = array(), $sidx = null, $sord = null, $limit = null, $start = null)
    {
        $catalogos = OportunidadesCatalogos::where(function($query) use ($clause){
            
            $this->_filtros($query, $clause);
            
        });
        
        if($sidx !== null && $sord !== null){$catalogos->orderBy($sidx, $sord);}
        if($limit != null){$catalogos->skip($start)->take($limit);}
        return $catalogos->get();
    }
    
    
    
}
