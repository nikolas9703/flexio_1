<?php

namespace Flexio\Modulo\Anticipos\Models;

trait Pagable
{


    public function pagos_por_aprobar()
    {
        return $this->morphToMany('Flexio\Modulo\Pagos\Models\Pagos', 'pagable', 'pag_pagos_pagables','pagable_id','pago_id')
        ->withPivot('monto_pagado','empresa_id')->where('pag_pagos.estado','por_aprobar')->withTimestamps();
    }

    public function pagos_anulados()
    {
        return $this->morphToMany('Flexio\Modulo\Pagos\Models\Pagos', 'pagable', 'pag_pagos_pagables','pagable_id','pago_id')
        ->withPivot('monto_pagado','empresa_id')->where('pag_pagos.estado','anulado')->withTimestamps();
    }

    public function pagos_no_anulados()
    {
        return $this->morphToMany('Flexio\Modulo\Pagos\Models\Pagos', 'pagable', 'pag_pagos_pagables','pagable_id','pago_id')
        ->withPivot('monto_pagado','empresa_id')->where('pag_pagos.estado','<>','anulado')->withTimestamps();
    }
}
