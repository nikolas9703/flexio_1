<?php
namespace Flexio\Modulo\Polizas\Models;

use Illuminate\Database\Eloquent\Model as Model;

class PolizasPrima extends Model
{
    protected $table        = 'pol_poliza_prima';    
    protected $fillable     = ['id_poliza', 'prima_anual', 'impuesto', 'otros', 'descuentos', 'total', 'frecuencia_pago', 'metodo_pago', 'fecha_primer_pago', 'cantidad_pagos', 'sitio_pago', 'centro_facturacion', 'direccion_pago'];
    protected $guarded      = ['id'];
}