<?php
namespace Flexio\Modulo\Planilla\Models\Abiertas;

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Colaboradores\Models\Colaboradores;
use Flexio\Modulo\Vacaciones\Models\Vacaciones;


class PlanillaVacacion extends Model
{
	protected $table = 'pln_planilla_vacacion';
	protected $fillable = [ 'planilla_id','vacacion_id','estado_ingreso_horas'];
	protected $guarded = ['id'];
	public $timestamps = false;

 	public function vacaciones(){
 		return $this->belongsToMany(Vacaciones::Class, 'pln_planilla_vacacion', 'planilla_id', 'vacacion_id');
 	}
	public function vacacion()
	{
			return $this->belongsTo(Vacaciones::Class, 'vacacion_id', 'id');
	 }


}
