<?php

namespace Flexio\Modulo\Anticipos\Services;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Anticipos\Models\Anticipo;
use Illuminate\Database\Eloquent\Builder;
use Flexio\Modulo\Anticipos\Providers;
use Flexio\Modulo\Anticipos\Providers\QueryFilters;
use Carbon\Carbon as Carbon;

class AnticipoFilters extends QueryFilters{

    public function q($q)
    {
        return $this->builder->where(function($query) use ($q){
            $query->where('codigo', 'like', "%$q%");
        });
    }

  function codigo($codigo){
    return $this->builder->where('codigo','like',"%".$codigo."%");
  }

  function empresa($empresa){
    return $this->builder->where('empresa_id',$empresa);
  }

  function proveedor($proveedor){
      return $this->builder->where('anticipable_id',$proveedor)->where('anticipable_type','Flexio\Modulo\Proveedores\Models\Proveedores');
  }

  function cliente($cliente){
    return $this->builder->where('anticipable_id',$cliente)->where('anticipable_type','Flexio\Modulo\Cliente\Models\Cliente');
  }
  function fecha_min($fecha){
    $fecha = Carbon::createFromFormat('d/m/Y', $fecha, 'America/Panama');
      return $this->builder->where('fecha_anticipo','>=',$fecha);
  }

  function fecha_max($fecha){
    $fecha = Carbon::createFromFormat('d/m/Y', $fecha, 'America/Panama');
      return $this->builder->where('fecha_anticipo','<=',$fecha);
  }

  function monto_min($monto){
      return $this->builder->where('monto','>=',$monto);
  }
  function monto_max($monto){
      return $this->builder->where('monto','<=',$monto);
  }


  function documento($documento){
            ///cambiar esto a clase
            return $this->builder->whereExists(function($query) use($documento){
              $query->select(Capsule::raw(1))->from('pro_proveedores')
              ->whereRaw('anticipable_id = pro_proveedores.id')
              ->where('nombre','like',"%".$documento."%");
            })->orWhereExists(function($query) use($documento){
              $query->select(Capsule::raw(1))->from('ord_ordenes')->join('empezables','ord_ordenes.id', '=', 'empezable_id')
              ->whereRaw('anticipo_id = atc_anticipos.id')
              ->where('ord_ordenes.numero','like',"%".$documento."%");
            })->orWhereExists(function($query) use($documento){
              $query->select(Capsule::raw(1))->from('sub_subcontratos')->join('empezables','sub_subcontratos.id', '=', 'empezable_id')
              ->whereRaw('anticipo_id = atc_anticipos.id')
              ->where('sub_subcontratos.codigo','like',"%".$documento."%");
            })
            ->select('atc_anticipos.*');
  }


  function anticipable_type($anticipable_type){
    if($anticipable_type=='proveedor'){
      $anticipable_type = 'Flexio\Modulo\Proveedores\Models\Proveedores';
    }
    if($anticipable_type=='cliente'){
      $anticipable_type = 'Flexio\Modulo\Cliente\Models\Cliente';
    }
      return $this->builder->where('anticipable_type',$anticipable_type);
  }

  function metodo_anticipo($metodo_anticipo){
      return $this->builder->where('metodo_anticipo',$metodo_anticipo);
  }

  function estado($estado){
      return $this->builder->where('estado',$estado);
  }

  function orden_compra($orden_id){
      return $this->builder->whereHas('orden_compra', function ($query) use($orden_id) {
          $query->where('empezable_id', $orden_id)
                ->where('empezable_type', 'Flexio\Modulo\OrdenesCompra\Models\OrdenesCompra');
      });
  }

  function subcontrato($subcontrato){
    return $this->builder->whereHas('subcontrato', function ($query) use($subcontrato) {
        $query->where('id', $subcontrato);
    });
  }

    public function modulo($modulo)
    {
        if($modulo == 'contratos' || $modulo == 'compras'){
            $codicion = $modulo == 'compras' ? "!=" : "=";
            return $this->builder
            ->select('atc_anticipos.*')
            ->leftJoin('empezables', function($join) use ($codicion){
                $join->on('atc_anticipos.id', '=', 'empezables.anticipo_id');
            })->where(function($q) use ($modulo, $codicion){
                $q->where("empezables.empezable_type", $codicion, 'Flexio\Modulo\SubContratos\Models\SubContrato');
                if($codicion == "!=")$q->orWhereRaw("empezables.empezable_type IS NULL");
            });
        }
        return $this->builder;
    }

    public function centro_contable_id($centro_contable){
       if(is_array($centro_contable)){
         return $this->builder->whereIn('centro_contable_id',$centro_contable);
       }
       return $this->builder->where('centro_contable_id',$centro_contable);
    }

}
