<?php
namespace Flexio\Modulo\Ramos\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;


class Ramos extends Model{


	protected $table = 'seg_ramos';
	protected $fillable = ['codigo','nombre','descripcion','codigo_ramo','id_tipo_int_asegurado','id_tipo_poliza','agrupador','empresa_id','padre_id'];
	protected $guarded = ['id'];
	
	public function roles()
	{
		return $this->hasMany('Flexio\Modulo\Ramos\Models\RamosRoles','id_ramo');
	}

	public function user()		
	{
		return $this->hasMany('Flexio\Modulo\Ramos\Models\RamosUsuarios','id_ramo');
	}
	
	
}
