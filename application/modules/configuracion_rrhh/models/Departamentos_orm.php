<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Departamentos_orm extends Model
{
	protected $table = 'dep_departamentos';
	protected $fillable = ['empresa_id', 'nombre', 'creado_por'];
	protected $guarded = ['id'];

	/**
	 * Retorna
	 * Lista de Departamentos que
	 * estan asociados a centro contable.
	 *
	 * @return [array]
	 */
	public static function lista($empresa_id=NULL){

		$result = Capsule::table('dep_departamentos AS d')
			//	->leftJoin('dep_departamentos_centros AS dc', 'dc.departamento_id', '=', 'd.id')
				//->where('dc.empresa_id', $empresa_id)
				->where('d.empresa_id', $empresa_id)
				->distinct()
				->get(array('d.id', 'd.nombre', 'd.estado'));

		return  (!empty($result) ? array_map(function($result)
						{
							 return array("id" => $result->id, "nombre" => $result->nombre, "estado" => $result->estado);
						 }, $result
						) : array());
	}

	/**
	 * Listado de Departamentos
	 *
	 * @return [array] [description]
	 */
	public static function listar($clause=NULL){

		$query = self::with(array('centros_contables' => function($query){
		}));

		//Si existen variables de limite
		if($clause!=NULL && !empty($clause) && is_array($clause))
		{
			foreach($clause AS $field => $value)
			{
				if($field == "cargo"){
					continue;
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

		$departamentos = $query->get()->toArray();
		$departamentos = (!empty($departamentos) ? array_map(function($departamentos){

			$centros = !empty($departamentos["centros_contables"]) ? $departamentos["centros_contables"] : array();
			$centros = !empty($centros) ? implode(", ", array_map(function($centros){ return $centros["nombre"]; }, $centros)) : "";

			return array(
				"id" => $departamentos["id"],
				"nombre" => $departamentos["nombre"],
				"estado" => ($departamentos["estado"]==1 ? 'Activo' : 'Inactivo'),
				"centro" => $centros
			);
		}, $departamentos) : "");

		return $departamentos;
	}

	public function centros_contables(){
		return $this->belongsToMany('Centros_orm','dep_departamentos_centros','departamento_id','centro_id');
	}

	/**
	 * Retorna lista de departamentos
	 * que estan relacionado a un centro
	 * y empresa especifico.
	 */
	public static function departamento_centro( $centro_id = NULL, $empresa_id = NULL){
		return Capsule::table('dep_departamentos_centros AS dc')
		->leftJoin('dep_departamentos AS d', 'd.id', '=', 'dc.departamento_id')
		->where('dc.empresa_id', '=', $empresa_id)
		->where('dc.centro_id', '=', $centro_id)
		->where('d.estado', '=', 1)
		->get(array('d.id', 'd.nombre'));
	}

	public static function departamento_centro3( $centro_id = NULL, $empresa_id = NULL){
		return Capsule::table('dep_departamentos_centros AS dc')
		 ->leftJoin('dep_departamentos AS d', 'd.id', '=', 'dc.departamento_id')
		->where('dc.empresa_id', '=', $empresa_id)
		->where('dc.centro_id', '=', $centro_id)
		->where('d.estado', '=', 1)
		->get(array('d.id', 'd.nombre'));
	}

        public static function departamento_centro2( $uuid_centro = NULL){
		return Capsule::table('dep_departamentos_centros AS dc')
		->leftJoin('dep_departamentos AS d', 'd.id', '=', 'dc.departamento_id')
                ->leftJoin('cen_centros AS cen', 'cen.id', '=', 'dc.centro_id')
		->where('cen.uuid_centro', '=', $uuid_centro)
		->where('d.estado', '=', 1)
		->get(array('d.id', 'd.nombre'));
	}


}
