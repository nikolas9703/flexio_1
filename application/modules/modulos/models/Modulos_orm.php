<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Modulos_orm extends Model
{
	protected $table = 'modulos';
	protected $fillable = ['nombre', 'descripcion','icono','controlador','version','tipo', 'grupo', 'agrupador','menu','agrupador_orden'];
	protected $guarded = ['id'];
	public $timestamps = false;

	/**
	 * Recursos
	 */
	public function recursos(){
		return $this->hasMany('Recursos_orm', 'modulo_id')->withPivot('recurso_id');
	}

	/**
	 * Listado de Modulos
	 *
	 * @return object
	 */
	public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
	{
		$query = self::where('tipo', 'addon')->whereNotIn('id', array(1,4));
	
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
