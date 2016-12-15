<?php
namespace Flexio\Modulo\Planilla\Models\Pagadas;

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class PagadasCalculos extends Model
{
	protected $table = 'pln_pagadas_calculos';
	protected $fillable = ['planilla_pagada_id', 'salario_mensual_promedio','salario_anual_promedio','total_devengado','indemnizacion_proporcional','fecha_creacion'];
	protected $guarded = ['id'];
	public $timestamps = false;

}
