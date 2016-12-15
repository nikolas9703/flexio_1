<?php 

namespace Flexio\Modulo\Ramos\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;


class RamosRoles extends Model {


	protected $table = 'seg_ramos_roles';
	protected $fillable = ['id_ramo','id_rol'];
	public $timestamps = false;

	public function rol()
	{
		return $this->belongsTo('Ramos');	
	}
}
