<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Estado_comision_orm extends Model
{
	protected $table = 'com_comisiones_cat';
	protected $fillable = ['id_campo', 'valor', 'etiqueta','identificador'];
	protected $guarded = ['id_cat'];
	public $timestamps = false;
	protected $primaryKey = 'id_cat';
	protected $appends     = ['color_estado'];

	public function getColorEstadoAttribute()
	{
			if($this->id_cat == 20) //Abierto, amarillo
				 $color = '#F8AC59';
			else if($this->id_cat == 14) //Turquesa
				$color = '#1AB394';
			else if($this->id_cat == 16) //Anulada
				$color = '#000000';
			/*else if($this->id_cat == 16) //Turquesa
				$color = '#23C6C8';*/
			else if($this->id_cat == 29) //Celeste
				$color = '#4fd4f2';
			else if($this->id_cat == 30) //Naranja
				$color = '#fd996b';
			/*else if($this->id_cat == 19) //Turquesa
				$color = '#23C6C8';*/
			else if($this->id_cat == 19) //Rojo
				$color = '#ED5565';
			else {
				$color = '#ffffff';
			}
				return $color;
	}

}
