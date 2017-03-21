<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Cargos_orm extends Model
{
	protected $table = 'carg_cargos';
	protected $fillable = ['empresa_id', 'departamento_id', 'codigo', 'nombre', 'descripcion', 'tipo_rata', 'rata', 'estado_id', 'creado_por'];
	protected $guarded = ['id'];
	
	public function departamentos()
	{
		return $this->hasOne('Departamentos_orm', 'id', 'departamento_id');
	}
	
	/*public function centros_contables(){
		return $this->belongsToMany('Centros_orm','dep_departamentos_centros','departamento_id','centro_id');
	}*/
	
	public function estado()
	{
		return $this->hasOne('Estado_orm', 'id_cat', 'estado_id');
	}
	
	public static function getEnumValues($table, $column)
	{
		$matches = array();
		$columns = Capsule::select(Capsule::raw("SHOW COLUMNS FROM $table WHERE Field = '$column'"));
		
		if(empty($columns)){
			return false;
		}
		
		$type = $columns[0]->Type;
		
		preg_match('/^enum\((.*)\)$/', $type, $matches, PREG_OFFSET_CAPTURE);
		
		$enum = array();
		foreach( explode(',', $matches[1][0]) as $value )
		{
			$v = trim( $value, "'" );
			$enum = array_add($enum, $v, $v);
		}
		return $enum;
	}
	
	/**
	 * Retorna listado de Cargos
	 * De la Empresa Actual
	 */
	public static function lista($empresa_id=NULL){
	
		if($empresa_id==NULL){
			return false;
		}

		return self::with(array('estado','departamentos'))->where('empresa_id', $empresa_id)->get()->toArray();
	}
	
	/**
	 * Listado de Cargos
	 *
	 * @return object
	 */
	public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
	{
		$departamento = !empty($clause["departamento"]) ? $clause["departamento"] : array();
		
		$query = self::with(array('departamentos' => function($query) use($departamento){
			if(!empty($sidx) && preg_match("/departamento/i", $sidx)){
				$query->orderBy("nombre", $sord);
			}
		}, 'estado'));
		
		//Filtrar Departamento
		if(!empty($departamento)){
			$departamentos = Departamentos_orm::where("nombre", $departamento[0], $departamento[1])->get(array('id'))->toArray();
			if(!empty($departamentos)){
				$departamentos_id = (!empty($departamentos) ? array_map(function($departamentos){ return $departamentos["id"]; }, $departamentos) : "");
				$query->whereIn("departamento_id", $departamentos_id);
			}
		}

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
			if(!preg_match("/departamento/i", $sidx)){
				$query->orderBy($sidx, $sord);
			}
		}
	
		//Si existen variables de limite
		if($limit!=NULL) $query->skip($start)->take($limit);
		
		//return $query->get(array('id', 'nombre', 'descripcion', 'superuser', 'default', Capsule::raw("IF(estado=1, 'Activo', 'Inactivo') AS estado"), Capsule::raw("IF(superuser=1, 'Si', 'No') AS superuserValue")));
		return $query->get();
	}
}
