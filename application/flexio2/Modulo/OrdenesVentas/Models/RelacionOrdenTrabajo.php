<?php

namespace Flexio\Modulo\OrdenesVentas\Models;

trait RelacionOrdenTrabajo{
    function orden_trabajo(){
        return $this->hasMany('Flexio\Modulo\OrdenesTrabajo\Models\OrdenTrabajo','orden_de_id')->where("orden_de",'orden_venta');
    }
}
