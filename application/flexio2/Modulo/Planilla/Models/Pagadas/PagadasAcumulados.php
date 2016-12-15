<?php
namespace Flexio\Modulo\Planilla\Models\Pagadas;
use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\ConfiguracionPlanilla\Models\Acumulados;

class PagadasAcumulados extends Model
{
	protected $table = 'pln_pagadas_acumulados';
	protected $fillable = ['planilla_pagada_id', 'nombre','acumulado','saldo','fecha_creacion','acumulado_id','acumulado_planilla'];
	protected $guarded = ['id'];
	public $timestamps = false;

	public function acumulado_info()
	{
			return $this->belongsTo(Acumulados::class, 'acumulado_id', 'id');
	 }

}
