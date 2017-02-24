<?php
namespace Flexio\Modulo\EntradaManuales\Services;

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Provider\QueryFilters;
use Carbon\Carbon as Carbon;

class EntradaManualQueryFilters extends QueryFilters{
    

    function empresa($empresa){
        return $this->builder->where('empresa_id',$empresa);
    }

    function centro_contable($centro_contable){
        if(is_array($centro_contable)){
            return $this->builder->whereHas('transacciones',function($query) use($centro_contable){
                $query->whereIn('centro_id',$centro_contable);
            });
        }
        return $this->builder->whereHas('transacciones',function($query) use($centro_contable){
            $query->where('centro_id',$centro_contable);
        });
    }


    function fecha_min($fecha){
         $fecha = Carbon::createFromFormat('d/m/Y', $fecha, 'America/Panama');
         return $this->builder->where('created_at','>=',$fecha);
    }

    function fecha_max($fecha){
        $fecha = Carbon::createFromFormat('d/m/Y', $fecha, 'America/Panama');
        return $this->builder->where('created_at','<=',$fecha);
    }

}
