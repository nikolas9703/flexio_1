<?php
namespace Flexio\Modulo\Planilla\Models\Pagadas;
use Flexio\Modulo\DescuentosDirectos\Models\DescuentosDirectos;

use \Illuminate\Database\Eloquent\Model as Model;
 use Carbon\Carbon;

class PagadasDescuentos extends Model
{
	protected $table = 'pln_pagadas_descuentos';
	protected $fillable = ['planilla_pagada_id', 'codigo','acreedor','monto_ciclo','saldo_restante','fecha_creacion', 'descuento_id','tipo_descuento_id','estado_pago_proveedor', 'created_at'];
	protected $guarded = ['id'];
	public $timestamps = true;
  protected $appends = ['fecha_creacion'];

    public function getFechaCreacionAttribute(){
      return Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['created_at'])->formatLocalized('%d de %B');
    }

    public function getCreatedAtAttribute($date) {
      return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('m-d-Y H:i:s');
    }


public function tipo_descuento(){
		return $this->hasOne('Estado_orm', 'id_cat', 'tipo_descuento');
	}

public function descuentos(){
		return $this->hasOne('Descuentos_orm', 'id', 'descuento_id')->select(array('id', 'monto_adeudado', 'tipo_descuento_id'));;

	}
  public function info_descuento(){
  		return $this->hasOne(DescuentosDirectos::Class, 'id', 'descuento_id')->select(array('id', 'monto_adeudado', 'tipo_descuento_id','plan_contable_id'));

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
