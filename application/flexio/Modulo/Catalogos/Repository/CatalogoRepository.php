<?php
namespace Flexio\Modulo\Catalogos\Repository;

//models
use Flexio\Modulo\Catalogos\Models\Catalogo;

class CatalogoRepository
{

    private function _filtros($query, $clause)
    {
        if(isset($clause['modulo']) && !empty($clause['modulo'])){$query->whereModulo($clause['modulo']);}
        if(isset($clause['tipo']) && !empty($clause['tipo'])){$query->whereTipo($clause['tipo']);}
        if(isset($clause['activo']) && !empty($clause['activo'])){$query->whereActivo($clause['activo']);}
        if(isset($clause['etiqueta']) && !empty($clause['etiqueta'])){$query->whereEtiqueta($clause['etiqueta']);}
        if(isset($clause['id']) && !empty($clause['id']) && is_array($clause['id'])){$query->whereIn("id", $clause['id']);}
        if(isset($clause['con_acceso']) && is_numeric($clause['con_acceso'])){$query->where("con_acceso", $clause['con_acceso']);}
    }

    public function get($clause = [])
    {
        return Catalogo::where(function($query) use ($clause){
            $this->_filtros($query, $clause);
        })->get();
    }

}
