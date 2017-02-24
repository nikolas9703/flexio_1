<?php
namespace Flexio\Modulo\FacturasCompras\Services;

use Flexio\Provider\QueryFilters;
use Carbon\Carbon as Carbon;

class FacturaCompraQueryFilters extends QueryFilters{

    public function empezable_type($empezable_type)
    {
        //invoice to pay
        if($empezable_type == 'factura')
        {
            return $this->builder->whereIn('faccom_facturas.estado_id',[14, 15]);
        }
        return $this->builder;
    }

    public function q($q)
    {
        return $this->builder->where(function($query) use ($q){
            $query->where('factura_proveedor', 'like', "%q%");
            $query->orWhereHas('proveedor', function($proveedor) use ($q){
                $proveedor->where('nombre', 'like', "%$q%");
            });
        });
    }

  function empresa($empresa){
    return $this->builder->where('empresa_id',$empresa);
  }

  function proveedor ($proveedor){
    return $this->builder->where('proveedor_id',$proveedor);
  }

  function subcontrato($subcontrato){
    return $this->builder->whereHas('subcontrato', function ($query) use($subcontrato) {
        $query->where('id', $subcontrato);
    });
  }
}
