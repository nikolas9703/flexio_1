<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Estado_orm extends Model
{
	protected $table = 'evc_evaluaciones_cat';
	protected $fillable = ['id_campo', 'valor', 'etiqueta'];
	protected $guarded = ['id'];
	public $timestamps = false;
	
	/**
	 * Retorna listado de Estados
	 */
	public static function lista(){
		return Capsule::table('evc_evaluaciones_campos AS colcam')
			->leftJoin('evc_evaluaciones_cat AS colcat', 'colcat.id_campo', '=', 'colcam.id_campo')
			->where('colcam.nombre_campo', '=', 'estado_id')
			->get(array('colcat.id_cat', 'colcat.etiqueta'));
	}
}