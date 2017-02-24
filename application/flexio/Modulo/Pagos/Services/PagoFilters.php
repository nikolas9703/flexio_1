<?php
namespace Flexio\Modulo\Pagos\Services;

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Provider\QueryFilters;
use Carbon\Carbon as Carbon;


class PagoFilters extends QueryFilters{

    function anticipo($anticipo){
        return $this->builder->where('empezable_id', $anticipo)->where('empezable_type','Flexio\Modulo\Anticipos\Models\Anticipo');
    }
    function subcontrato($subcontrato){
        return $this->builder->whereHas("facturas", function($factura) use ($subcontrato){
            $factura->where("faccom_facturas.operacion_id", $subcontrato)
                    ->where("faccom_facturas.operacion_type", "Flexio\Modulo\SubContratos\Models\SubContrato");
        });
    }
}
