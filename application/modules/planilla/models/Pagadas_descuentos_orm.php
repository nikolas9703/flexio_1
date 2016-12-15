<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon;

class Pagadas_descuentos_orm extends Model
{
	protected $table = 'pln_pagadas_descuentos';
	protected $fillable = ['planilla_pagada_id', 'codigo','acreedor','monto_ciclo','saldo_restante','fecha_creacion', 'descuento_id','tipo_descuento_id'];
	protected $guarded = ['id'];
	public $timestamps = false;
 

		public function getFechaCreacionAttribute(){
			return Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['created_at'])->formatLocalized('%d de %B');
		}

public function tipo_descuento(){
		return $this->hasOne('Estado_orm', 'id_cat', 'tipo_descuento');
	}

public function descuentos(){
		return $this->hasOne('Descuentos_orm', 'id', 'descuento_id')->select(array('id', 'monto_adeudado', 'tipo_descuento_id'));;

	}
public function pagadas(){
		return $this->hasOne('Pagadas_orm', 'id', 'planilla_pagada_id')->select(array('id', 'fecha_pago'));;

	}
public static function estados($clause=array())
	{

		$query = self::with(array('descuentos', 'tipo_descuento', 'pagadas' => function($query){
		}));
		foreach($clause AS $field => $value)
			{

                //Verificar si el campo tiene el simbolo @ y removerselo.
				if(preg_match('/@/i', $field)){
					$field = str_replace("@", "", $field);
				}
                if(is_array($value)){

					if(preg_match("/(fecha)/i", $field)){
						$query->where($field, $value['0'], $value['1']);

					}else{
						$query->whereIn("id", $value);
					}
				}else{
					$query->where($field, '=', $value);
				}
                        }

		return $query->get();

	}


}
