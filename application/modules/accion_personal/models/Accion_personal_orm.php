<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Accion_personal_orm extends Model
{
    protected $table = 'ap_acciones_personal';
	protected $fillable = ['empresa_id', 'accionable_id', 'accionable_type', 'no_accion', 'colaborador_id', 'centro_contable_id', 'departamento_id', 'cargo_id', 'nombre_completo', 'centro_contable', 'departamento', 'cargo', 'cedula'];
	protected $guarded = ['id'];
	
	public function accionable(){
		return $this->morpTo();
	}
	
	public function vacaciones(){
		return $this->belongsTo('Vacaciones_orm', 'accionable_id');
	}
	
	public function ausencias(){
		return $this->belongsTo('Ausencias_orm', 'accionable_id');
	}
	
	public function incapacidades(){
		return $this->belongsTo('Incapacidades_orm', 'accionable_id');
	}
	
	public function licencias(){
		return $this->belongsTo('Licencias_orm', 'accionable_id');
	}
	
	public function permisos(){
		return $this->belongsTo('Permisos_trabajo_orm', 'accionable_id');
	}
	
	public function liquidaciones(){
		return $this->belongsTo('Liquidaciones_orm', 'accionable_id');
	}
	
	public function evaluaciones(){
		return $this->belongsTo('Evaluaciones_orm', 'accionable_id');
	}
	
	public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
	{
		/*$cargo 			= !empty($clause["cargo"]) ? $clause["cargo"] : array();
		$departamentos 	= !empty($clause["departamento_id"]) ? $clause["departamento_id"] : array();
		$colaboradores 	= !empty($clause["colaborador"]) ? $clause["colaborador"] : array();
		$nombre_centro = !empty($clause["nombre_centro"]) ? $clause["nombre_centro"] : array();*/
	
		$query = Accion_personal_orm::with(array('ausencias' => function($query) use($sidx, $sord){
        	$query->with(['colaborador', 'estado']);
        	
        	if(!empty($sidx) && preg_match("/estado/i", $sidx)){
        		$query->orderBy("estado_id", $sord);
        	}
        },'vacaciones' => function($query) use($sidx, $sord){
        	$query->with(['colaborador', 'estado']);
        	
        	if(!empty($sidx) && preg_match("/estado/i", $sidx)){
        		$query->orderBy("estado_id", $sord);
        	}
        }, 'incapacidades' => function($query) use($sidx, $sord){
        	$query->with(['colaborador', 'estado']);
        	
        	if(!empty($sidx) && preg_match("/estado/i", $sidx)){
        		$query->orderBy("estado_id", $sord);
        	}
        }, 'licencias' => function($query) use($sidx, $sord){
        	$query->with(['colaborador', 'estado']);
        	
        	if(!empty($sidx) && preg_match("/estado/i", $sidx)){
        		$query->orderBy("estado_id", $sord);
        	}
        }, 'permisos' => function($query) use($sidx, $sord){
        	$query->with(['colaborador', 'estado']);
        	
        	if(!empty($sidx) && preg_match("/estado/i", $sidx)){
        		$query->orderBy("estado_id", $sord);
        	}
        }, 'liquidaciones' => function($query) use($sidx, $sord){
        	$query->with(['colaborador', 'estado']);
        	
        	if(!empty($sidx) && preg_match("/estado/i", $sidx)){
        		$query->orderBy("estado_id", $sord);
        	}
        }, 'evaluaciones' => function($query) use($sidx, $sord){
        	$query->with(['colaborador', 'estado']);
        	
        	if(!empty($sidx) && preg_match("/estado/i", $sidx)){
        		$query->orderBy("estado_id", $sord);
        	}
        }));
	
		//Filtros para cuando se muestra la tabla en modulo de Planilla/Nomina
		if(!empty($clause["vacacion_id"])){
			$query->where("accionable_type", "Vacaciones_orm")->whereIn("accionable_id", $clause["vacacion_id"]);
		}
		if(!empty($clause["licencia_id"])){
			$query->where("accionable_type", "Licencias_orm")->whereIn("accionable_id", $clause["licencia_id"]);
		}
		if(!empty($clause["liquidacion_id"])){
			$query->where("accionable_type", "Liquidaciones_orm")->whereIn("accionable_id", $clause["liquidacion_id"]);
		}
		
		//Si existen variables de limite
		if($clause!=NULL && !empty($clause) && is_array($clause))
		{
			foreach($clause AS $field => $value)
			{
				if($field == "liquidacion_id" || $field == "vacacion_id" || $field == "licencia_id" ||   $field == "colaborador"  || $field == "id" || $field == "nombre_centro"){
					continue;
				}

				//Concatenar Nombre y Apellido para busqueda
				if($field == "nombre"){
					$field = Capsule::raw("CONCAT_WS(' ', IF(nombre != '', nombre, ''), IF(apellido != '', apellido, ''))");
				}

				//Verificar si el campo tiene el simbolo @ y removerselo.
				if(preg_match('/@/i', $field)){
					$field = str_replace("@", "", $field);
				}

				//verificar si valor es array
				if(is_array($value)){
					$query->where($field, $value[0], $value[1]);
				}else{
					$query->where($field, '=', $value);
				}
			}
		}

		//Si existen variables de orden
		if($sidx!=NULL && $sord!=NULL){
			if(!preg_match("/(estado)/i", $sidx)){
				$query->orderBy($sidx, $sord);
			}
		}

		//Si existen variables de limite
		if($limit!=NULL) $query->skip($start)->take($limit);
		return $query->get();
	}
}