<?php
namespace Flexio\Modulo\Catalogos\Repository;

//models
use Flexio\Modulo\Catalogos\Models\Catalogo;

class CatalogoRepository
{
    
    private function _filtros($query, $clause)
    {
        if(isset($clause['modulo']) && !empty($clause['modulo'])){$query->whereModulo($clause['modulo']);}
    }

    public function get($clause = [])
    {
        return Catalogo::where(function($query) use ($clause){
            $this->_filtros($query, $clause);
        })->get();
    }

}
