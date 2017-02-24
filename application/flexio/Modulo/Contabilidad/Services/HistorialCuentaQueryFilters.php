<?php

namespace Flexio\Modulo\Contabilidad\Services;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Provider\QueryFilters;
use Carbon\Carbon as Carbon;

class HistorialCuentaQueryFilters extends QueryFilters{

    function codigo($codigo){
        return $this->builder->where('nombre','like',"%".$codigo."%");
    }

    function empresa($empresa){
        return $this->builder->where('empresa_id',$empresa);
    }
    function cuenta_ids($cuenta_id){
        if(is_array($cuenta_id)){
            return $this->builder->whereIn('cuenta_id',$cuenta_id);
        }

        return $this->builder->where('cuenta_id',$cuenta_id);

    }

    function id($id){
        if(is_array($id)){
            return $this->builder->whereIn('id',$id);
        }

        return $this->builder->where('id',$id);
    }

    function centro_contable($centro_contable){
        if(is_array($centro_contable)){
            return $this->builder->whereIn('centro_id',$centro_contable);
        }
        return $this->builder->where('centro_id',$centro_contable);
    }

    function fecha_min($fecha){
        $fecha = Carbon::createFromFormat('d/m/Y', $fecha, 'America/Panama');
          return $this->builder->where('created_at','>=',$fecha);
    }

    function fecha_max($fecha){
        $fecha = Carbon::createFromFormat('d/m/Y', $fecha, 'America/Panama');
          return $this->builder->where('created_at','<=',$fecha);
    }

    public function cuentas($cuentas)
    {
        if(is_array($cuentas)){
            return $this->builder->whereIn('contab_cuentas.id', $this->limpiar_cuentas($cuentas));
        }
        return $this->builder;
    }

    private function limpiar_cuentas($cuentas)
    {
        return array_map(function($cuenta){
            $cuenta = str_replace('activo:', '', $cuenta);
            $cuenta = str_replace('ingreso:', '', $cuenta);
            $cuenta = str_replace('costo:', '', $cuenta);
            return str_replace('variante:', '', $cuenta);
        }, $cuentas);
    }
}
