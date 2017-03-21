<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Configuracion_orm extends Model
{
	protected $table = 'pln_planilla_configuracion';
	protected $fillable = ['fecha_inicial', 'id_empresa'];
	protected $guarded = ['id'];
	public $timestamps = false;
}