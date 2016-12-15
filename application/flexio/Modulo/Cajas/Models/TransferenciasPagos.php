<?php
namespace Flexio\Modulo\Cajas\Models;

use \Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Pagos\Models\PagosMetodos;

class TransferenciasPagos extends Model
{
    protected $table    = 'ca_transferencia_pagos';
    protected $fillable = ['transferencia_id', 'tipo_pago_id', 'monto', 'no_cheque', 'banco'];
    protected $guarded	= ['id'];



     public function pago_info()
    {
        return $this->hasMany(PagosMetodos::Class, 'id', 'tipo_pago_id');
    }

}
