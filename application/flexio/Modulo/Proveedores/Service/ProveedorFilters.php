<?php
namespace Flexio\Modulo\Proveedores\Service;

use Flexio\Provider\QueryFilters;
use Carbon\Carbon as Carbon;

class ProveedorFilters extends QueryFilters{

    public function empezable_type($empezable_type)
    {
        //invoice to pay
        if($empezable_type == 'proveedor')
        {
            return $this->builder;
//            return $this->builder->whereHas('facturas',function($query){
//                $query->whereIn('faccom_facturas.estado_id',[14, 15]);
//            });
        }
        return $this->builder;
    }

    public function q($q)
    {
        return $this->builder->where(function($query) use ($q){
            $query->where('nombre', 'like', "%$q%");
        });
    }

    public function empresa($empresa)
    {
      return $this->builder->where('id_empresa',$empresa);
    }
}
