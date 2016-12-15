<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Estado_orm extends Model
{
	protected $table = 'desc_descuentos_cat';
	protected $fillable = ['id_campo', 'valor', 'etiqueta'];
	protected $guarded = ['id'];
	public $timestamps = false;
	
	/**
	 * Retorna listado de Estados
	 */
	public static function lista(){
		return Capsule::table('desc_descuentos_campos AS desccam')
			->leftJoin('desc_descuentos_cat AS desccat', 'desccat.id_campo', '=', 'desccam.id_campo')
			->where('desccam.nombre_campo', '=', 'estado_id')
			->get(array('desccat.id_cat', 'desccat.etiqueta'));
	}
}