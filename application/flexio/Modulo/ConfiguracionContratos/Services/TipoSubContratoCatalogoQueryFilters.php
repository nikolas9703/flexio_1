<?php
/**
 * Created by PhpStorm.
 * User: Ivan Cubilla
 * Date: 22/2/17
 * Time: 10:58 AM
 */

namespace Flexio\Modulo\ConfiguracionContratos\Services;

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Provider\QueryFilters;
use Carbon\Carbon as Carbon;

class TipoSubContratoCatalogoQueryFilters extends QueryFilters
{
    function empresa_id($valor){
        return $this->builder->where('empresa_id',$valor);
    }

    function nombre($valor){
        return $this->builder->where('nombre',$valor);
    }

    function acceso($valor){
        return $this->builder->where('acceso',$valor);
    }

    function estado($valor){
        return $this->builder->where('estado',$valor);
    }
}