<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Deducciones_orm extends Model
{
	protected $table = 'col_deducciones';
	protected $fillable = ['colaborador_id', 'tipo_deduccion_id', 'nombre', 'cedula', 'edad', 'relacion_id', 'deduccion', 'creado_por'];
	protected $guarded = ['id'];
}