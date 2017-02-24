<?php

namespace Flexio\Modulo\Documentos\Services;

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Provider\QueryFilters;
use Carbon\Carbon as Carbon;


class TipoDocumentoFilters extends QueryFilters{


    public function empresa($empresa)
    {
        return $this->builder->where('empresa_id', $empresa);
    }
    public function estado($estado)
    {
        return $this->builder->where('estado', $estado);
    }

}
