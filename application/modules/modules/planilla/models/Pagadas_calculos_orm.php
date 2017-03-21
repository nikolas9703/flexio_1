 <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Pagadas_calculos_orm extends Model
{
	protected $table = 'pln_pagadas_calculos';
	protected $fillable = ['planilla_pagada_id', 'salario_mensual_promedio','salario_anual_promedio','total_devengado','indemnizacion_proporcional','fecha_creacion'];
	protected $guarded = ['id'];
	public $timestamps = false;
}