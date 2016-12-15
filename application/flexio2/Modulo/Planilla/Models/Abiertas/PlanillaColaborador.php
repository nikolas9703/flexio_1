<?php
namespace Flexio\Modulo\Planilla\Models\Abiertas;

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;


class PlanillaColaborador extends Model
{
	protected $table = 'pln_planilla_colaborador';
	protected $fillable = [ 'planilla_id','colaborador_id','estado_ingreso_horas'];
	protected $guarded = ['id'];
	public $timestamps = false;

 	public function colaboradores(){
 		return $this->belongsToMany('Colaboradores_orm', 'pln_planilla_colaborador', 'planilla_id', 'colaborador_id');
 	}
    //  return $this->belongsToMany(Colaboradores::Class, 'pln_planilla_colaborador', 'planilla_id', 'colaborador_id');
   public function colaborador()
   {
       return $this->belongsTo(Colaboradores::Class, 'colaborador_id', 'id');
    }

 	public function ingreso_horas()
 	{
  	     return $this->hasMany('Ingreso_horas_orm', 'id_planilla_colaborador', 'id');
 	}

}
