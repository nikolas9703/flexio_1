<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Estado_orm extends Model
{
		protected $table = 'pln_planilla_cat';
		protected $fillable = ['id_campo', 'valor', 'etiqueta'];
		protected $guarded = ['id_cat'];
		public $timestamps = false;
		protected $primaryKey = 'id_cat';
		protected $appends     = ['color_estado'];

	public static function lista(){
 		return self::where('id_campo', 0)->get()->toArray();
	}


	public function getColorEstadoAttribute()
	{
			if($this->id_cat == 13) //Abierto
			   $color = '#F8AC59';
			else if($this->id_cat == 14)
				$color = '#1AB394';
			else if($this->id_cat == 15)
				$color = '#000000';
			else if($this->id_cat == 16)
				$color = '#23C6C8';
			else if($this->id_cat == 29)
				$color = '#4fd4f2';
			else if($this->id_cat == 30)
				$color = '#fd996b';
			else if($this->id_cat == 31)
				$color = '#23C6C8';
			else if($this->id_cat == 32)
				$color = '#ED5565';
			else {
				$color = '#ffffff';
			}
 			return $color;
	}

}
