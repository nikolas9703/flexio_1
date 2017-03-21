<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Experiencia_laboral_orm extends Model
{
	protected $table = 'col_experiencia_laboral';
	protected $fillable = ['colaborador_id', 'nombre', 'empresa', 'fecha_salida', 'devengado_total', 'fecha_devengado_desde', 'fecha_devengado_hasta', 'fecha_creacion'];
	protected $guarded = ['id'];
}