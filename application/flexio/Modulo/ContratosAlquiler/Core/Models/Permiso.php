<?php 

namespace Flexio\Modulo\Core\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Permiso extends Model{

	protected $table = 'permisos';


	public function recurso(){
		return $this->belongsTo('Flexio\Modulo\Core\Models\Recurso');
	}

	  public function roles(){
        return $this->belongsToMany('Flexio\Modulo\Roles\Models\Roles','roles_permisos','permiso_id','rol_id')
        ->whereNotIn('rol_id',[2,3]);
    }

}
