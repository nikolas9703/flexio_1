<?php
namespace Flexio\Modulo\EntradaManuales\Services;

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Provider\QueryFilters;
use Carbon\Carbon as Carbon;

class TransaccionQueryFilters extends QueryFilters{


    function empresa($empresa){
        return $this->builder->where('empresa_id',$empresa);
    }

    function entrada_manual($id){
        
        return $this->builder
               ->where('transaccionable_id',$id)
               ->where('transaccionable_type','Flexio\Modulo\EntradaManuales\Models\EntradaManual');
    }

    function centro_contable($centro_contable){
        if(is_array($centro_contable)){
            return $this->builder->whereIn('centro_id',$centro_contable);
        }
        return $this->builder->where('centro_id',$centro_contable);
    }


    function fecha_min($fecha){
         $fecha = Carbon::createFromFormat('d/m/Y', $fecha, 'America/Panama');
         return $this->builder->where('fecha_pago','>=',$fecha);
    }

    function fecha_max($fecha){
        $fecha = Carbon::createFromFormat('d/m/Y', $fecha, 'America/Panama');
        return $this->builder->where('fecha_pago','<=',$fecha);
    }

}
