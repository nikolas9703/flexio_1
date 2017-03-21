<?php
namespace Flexio\Modulo\Pagos\Models;

use Illuminate\Database\Eloquent\Model as Model;

class PagosPagables extends Model
{

    protected $table = 'pag_pagos_pagables';
    protected $fillable = ['pago_id','pagable_id','pagable_type','monto_pagado','empresa_id'];
    protected $guarded = ['id'];
}