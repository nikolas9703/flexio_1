<?php
namespace Flexio\Modulo\Planilla\Models\Pagadas;

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class PagadasIngresos extends Model
{
	protected $table = 'pln_pagadas_ingresos';
	protected $fillable =
		[
			'planilla_pagada_id',
			'detalle',
			'cantidad_horas',
			'rata',
			'calculo',
			'beneficio_cuenta_id',
			'beneficio_id',
			'recargo_cuenta_id',
			'recargo_id',
			'recargo_monto',
			'beneficio_monto',
			'fecha_creacion'
		];
	protected $guarded = ['id'];
	public $timestamps = false;


}
