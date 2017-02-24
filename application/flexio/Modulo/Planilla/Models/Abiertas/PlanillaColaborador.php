<?php
namespace Flexio\Modulo\Planilla\Models\Abiertas;

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Colaboradores\Models\Colaboradores;


class PlanillaColaborador extends Model
{
	protected $table = 'pln_planilla_colaborador';
	protected $fillable = [ 'planilla_id','colaborador_id','estado_ingreso_horas'];
	protected $guarded = ['id'];
	public $timestamps = false;
	protected $appends = ['cantidad_horas_total'];

 	public function colaboradores(){
 		return $this->belongsToMany('Colaboradores_orm', 'pln_planilla_colaborador', 'planilla_id', 'colaborador_id');
 	}

  public function colaborador()
  {
       return $this->belongsTo(Colaboradores::Class, 'colaborador_id', 'id');
  }

 	public function ingreso_horas()
 	{
  	     return $this->hasMany('Ingreso_horas_orm', 'id_planilla_colaborador', 'id');
 	}

	public function getCantidadHorasTotalAttribute() {

      $total_horas = 0;
      $total_horas = $this->ingreso_horas->sum('cantidad_horas');
       return $total_horas;
  }
}
