<?php
namespace Flexio\Modulo\Polizas\Models;

use Illuminate\Database\Eloquent\Model as Model;

class PolizasVigencia extends Model
{
    protected $table        = 'pol_poliza_vigencia';    
    protected $fillable     = ['id_poliza', 'vigencia_desde', 'vigencia_hasta', 'suma_asegurada', 'tipo_pagador', 'pagador', 'poliza_declarativa'];
    protected $guarded      = ['id'];
   
    
}