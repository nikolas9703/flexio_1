<?php
namespace Flexio\Modulo\SubContratos\Services;

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Provider\QueryFilters;
use Carbon\Carbon as Carbon;

class SubContratoQueryFilters extends QueryFilters{

    public function empezable_type($empezable_type)
    {
        //invoice to pay
        if($empezable_type == 'subcontrato')
        {
            return $this->builder->whereHas('facturas',function($query){
                $query->whereIn('faccom_facturas.estado_id',[14, 15]);
            });
        }
        return $this->builder;
    }

    public function q($q)
    {
        return $this->builder->where(function($query) use ($q){
            $query->where('sub_subcontratos.codigo', 'like', "%$q%");
            $query->orWhereHas('proveedor', function($proveedor) use ($q){
                $proveedor->where('pro_proveedores.nombre', 'like', "%$q%");
            });
        });
    }

  function codigo($codigo){
    return $this->builder->where('codigo','like',"%".$codigo."%");
  }

  function empresa($empresa){
    return $this->builder->where('empresa_id',$empresa);
  }

  function proveedor ($proveedor){
    return $this->builder->where('proveedor_id',$proveedor);
  }

  function tipo_subcontrato ($tipo_subcontrato_id){
    return $this->builder->where('tipo_subcontrato_id',$tipo_subcontrato_id);
  }


  function tipo_subcontrato_acceso ($tipos_subcontratos){
    return $this->builder->whereIn('tipo_subcontrato_id', collect($tipos_subcontratos)->toArray());
  }

  function monto_min($monto){
    return $this->builder->whereHas('subcontrato_montos', function($q) use ($monto){
            $q->groupBy('sub_subcontratos_montos.subcontrato_id');
            $q->havingRaw('sum(sub_subcontratos_montos.monto) >= '.$monto);
          });
  }

  function monto_max($monto){
    return $this->builder->whereHas('subcontrato_montos', function($q) use ($monto){
            $q->groupBy('sub_subcontratos_montos.subcontrato_id');
            $q->havingRaw('sum(sub_subcontratos_montos.monto) <= '.$monto);
        });
  }

  function centro_contable($centro_contable){
    return $this->builder->where('centro_id',$centro_contable);
  }

  function estado($estado){
      return $this->builder->where('estado',$estado);
  }

  function factura($factura_id) {
      return $this->builder->where('empezable_id', $factura_id)->where('empezable_type','Flexio\Modulo\FacturasVentas\Models\FacturaVenta');
  }

    public function centros_contables($centros)
    {
        if(!in_array('todos', $centros))
        {
            return $this->builder->whereIn("sub_subcontratos.centro_id", $centros);
        }
        return $this->builder;
    }

}
