<?php
namespace Flexio\Modulo\Ramos\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;


class RamosUsuarios extends Model {


	protected $table = 'seg_ramos_usuarios';
	protected $fillable = ['id_ramo','id_usuario'];
	public $timestamps = false;

	public function users()
	{
		return $this->belongsTo('Ramos');
	}
	
}
