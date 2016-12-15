<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Planilla_acumulados_orm extends Model
{
	protected $table = 'pln_planilla_acumulados';
	protected $fillable = ['acumulado_id', 'planilla_id','fecha_creacion'];
	protected $guarded = ['id'];
	public $timestamps = false;
	
	public static function   lista_acumulados_planilla($planilla_id = NULL ){
	
		$resultado_acumulados = array();
	
		$acumulados = Capsule::table('pln_planilla_acumulados as acum')
		->leftJoin('pln_config_acumulados as config_acum', 'config_acum.id', '=', 'acum.acumulado_id')
		->leftJoin('pln_config_acumulados_constructores as constr', 'constr.acumulado_id', '=', 'acum.acumulado_id')
		->where('acum.planilla_id', $planilla_id)
		->distinct()
		->get();
		if(!empty($acumulados)){
			foreach ($acumulados as  $row){
				$resultado_acumulados[] = array(
						"id"	=> $row->id,
						"nombre"	=> $row->nombre,
						"operador" => $row->operador_valor,
						"tipo_calculo_uno" => $row->tipo_calculo_uno,
						"valor_calculo_uno" => $row->valor_calculo_uno,
						"tipo_calculo_dos" => $row->tipo_calculo_dos,
						"valor_calculo_dos" =>$row->valor_calculo_dos
				);
			}
	
		}
	
		return $resultado_acumulados;
	}
	
}