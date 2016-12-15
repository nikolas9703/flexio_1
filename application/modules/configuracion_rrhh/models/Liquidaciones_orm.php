<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Liquidaciones_orm extends Model
{
	protected $table = 'liq_configuracion';
	protected $fillable = ['empresa_id', 'nombre', 'creado_por', 'estado'];
	protected $guarded = ['id'];
	public $timestamps = true;

	/**
	 * Listado de Tiempos
	 *
	 * @return [array] [description]
	 */
	public static function listar($clause=NULL){

		$query = self::with(array());

		//Si existen variables de limite
		if($clause!=NULL && !empty($clause) && is_array($clause))
		{
			foreach($clause AS $field => $value)
			{
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

		return $query->get();
	}
}
