<?php

namespace Flexio\Modulo\Anticipos\Models;

trait OrdenVentaAnticipable
{
    public function orden_venta_facturada_completo()
    {
       return $this->morphedByMany('Flexio\Modulo\OrdenesVentas\Models\OrdenVenta', 'empezable')->where('estado','facturado_completo');
    }
}
