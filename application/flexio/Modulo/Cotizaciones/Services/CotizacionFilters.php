<?php
namespace Flexio\Modulo\Cotizaciones\Services;

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Provider\QueryFilters;
use Carbon\Carbon;


class CotizacionFilters extends QueryFilters
{

    public function cliente($cliente_id)
    {
        return $this->builder->where('cliente_tipo', 'cliente')
        ->where('cliente_id', $cliente_id);
    }

    function empresa($empresa){
      return $this->builder->where('empresa_id',$empresa);
    }

    function creado_por($creado_por){
      return $this->builder->where('creado_por',$creado_por);
    }



}
