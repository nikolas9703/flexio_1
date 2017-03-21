<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon;

class Evaluaciones_orm extends Model
{
	protected $table = 'evc_evaluaciones';
	protected $fillable = ['empresa_id', 'colaborador_id', 'numero', 'fecha', 'tipo_evaluacion_id', 'centro_contable_id', 'departamento_id', 'cargo_id', 'evaluado_por', 'calificacion', 'resultado_id', 'estado_id', 'observaciones', 'documento_evaluacion', 'archivo_ruta', 'archivo_nombre', 'creado_por'];
	protected $guarded = ['id'];
	
	function acciones(){
		return $this->morphMany('Accion_personal_orm', 'accionable');
	}
	
	public function colaborador()
	{
		return $this->hasOne('Colaboradores_orm', 'id', 'colaborador_id');
	}
	
	public function usuario()
	{
		return $this->hasOne('Usuario_orm', 'id', 'creado_por');
	}
	
	public function tipo_evaluacion()
	{
		return $this->hasOne('Estado_orm', 'id_cat', 'tipo_evaluacion_id');
	}
	
	public function estado(){
		return $this->hasOne('Estado_orm', 'id_cat', 'estado_id');
	}
	
	public function centro_contable(){
		return $this->hasOne('Centros_orm', 'id', 'centro_contable_id');
	}
	
	public function resultado()
	{
		return $this->hasOne('Catalogo_orm', 'id_cat', 'resultado_id');
	}
	
	public function departamento(){
		return $this->hasOne('Departamentos_orm', 'id', 'departamento_id');
	}
	
	public function cargo(){
		return $this->hasOne('Cargos_orm', 'id', 'cargo_id');
	}
	
	/**
	 * Listado de Evaluaciones
	 *
	 * @return object
	 */
	public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
	{
		$query = self::with(array('tipo_evaluacion', 'resultado', 'colaborador',
		'usuario' => function($query) use($sidx, $sord){
			if(!empty($sidx) && preg_match("/creado_por/i", $sidx)){
				$query->orderBy("nombre", $sord);
			}
		},
		'centro_contable' => function($query) use($sidx, $sord){
			if(!empty($sidx) && preg_match("/centro_contable/i", $sidx)){
				$query->orderBy("nombre", $sord);
			}
		},
		'departamento' => function($query) use($sidx, $sord){
			if(!empty($sidx) && preg_match("/departamento/i", $sidx)){
				$query->orderBy("nombre", $sord);
			}
		}, 
		'cargo' => function($query) use($sidx, $sord){
			if(!empty($sidx) && preg_match("/cargo/i", $sidx)){
				$query->orderBy("nombre", $sord);
			}
		})); 
	
		//Si existen variables de limite
		if($clause!=NULL && !empty($clause) && is_array($clause))
		{
			foreach($clause AS $field => $value)
			{
				if($field == "departamento"){
					continue;
				}

				if(is_array($value)){
					$query->where($field, $value[0], $value[1]);
				}else{
					$query->where($field, '=', $value);
				}
			}
		}

		//Si existen variables de orden
		if($sidx!=NULL && $sord!=NULL){
			if(!preg_match("/(cargo|departamento|centro_contable)/i", $sidx)){
				$query->orderBy($sidx, $sord);
			}
		}

		//Si existen variables de limite
		if($limit!=NULL) $query->skip($start)->take($limit);

		return $query->get();
	}
}