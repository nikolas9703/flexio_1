<?php 

namespace Flexio\Modulo\Core\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Recurso extends Model{

	protected $table = 'recursos';

	public function permisos(){
		return $this->hasMany(Permiso::class);
	}

}

