<?php
namespace Flexio\Modulo\Planilla\Models\Pagadas;
use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\ConfiguracionPlanilla\Models\Deducciones;

class PagadasDeducciones extends Model
{
	protected $table = 'pln_pagadas_deducciones';
	protected $fillable = ['planilla_pagada_id', 'deduccion_id','nombre','descuento','descuento_patronal','saldo','fecha_creacion'];
	protected $guarded = ['id'];
	public $timestamps = false;


	public function deduccion_info()
	{
			return $this->belongsTo(Deducciones::Class, 'deduccion_id', 'id');
	 }

}
