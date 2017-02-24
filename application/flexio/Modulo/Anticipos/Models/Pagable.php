<?php

namespace Flexio\Modulo\Anticipos\Models;

trait Pagable
{
    public function getPagoPagadoAttribute(){
        return $this->pagos_pagados->sum('monto_pagado');
    }

    public function getPagoSaldoAttribute(){
        return $this->attributes['monto'] - $this->pago_pagado;
    }

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

    public function pagos_pagados(){
        return $this->morphToMany('Flexio\Modulo\Pagos\Models\Pagos', 'pagable', 'pag_pagos_pagables','pagable_id','pago_id')
        ->withPivot('monto_pagado','empresa_id')->where('pag_pagos.estado','aplicado');
    }

    public function pagos_no_anulados()
    {
        return $this->morphToMany('Flexio\Modulo\Pagos\Models\Pagos', 'pagable', 'pag_pagos_pagables','pagable_id','pago_id')
        ->withPivot('monto_pagado','empresa_id')->where('pag_pagos.estado','<>','anulado')->withTimestamps();
    }

    //function es utilizada en el empezar desde de pagos
    public function no_anulados(){
        return $this->morphToMany('Flexio\Modulo\Pagos\Models\Pagos', 'pagable', 'pag_pagos_pagables','pagable_id','pago_id')
        ->withPivot('monto_pagado','empresa_id')->whereIn('pag_pagos.estado',['por_aprobar','por_aplicar','aplicado']);
    }
}
