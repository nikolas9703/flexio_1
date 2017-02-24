<?php
namespace Flexio\Modulo\Usuarios\Service;

use Flexio\Provider\QueryFilters;
use Carbon\Carbon as Carbon;

class UsuarioFilters extends QueryFilters{

    public function q($q)
    {
        return $this->builder->where(function($query) use ($q){
            $query->where('nombre', 'like', "%$q%");
            $query->orWhere('apellido', 'like', "%$q%");
        });
    }

    public function empresa($empresa_id)
    {
        $this->builder->whereHas('empresas', function($empresa) use ($empresa_id){
            $empresa->where('empresas.id', $empresa_id);
        });
    }
}
