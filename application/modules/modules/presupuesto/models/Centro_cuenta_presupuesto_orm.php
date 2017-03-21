<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Centro_cuenta_presupuesto_orm extends Model
{
	protected $table = 'pres_presupuesto_cuenta_centro';
	protected $fillable = ['presupuesto_id','cuentas_id','empresa_id','centro_contable_id','montos','info_presupuesto'];
	protected $guarded = ['id'];

  // public function __construct(array $attributes = array()){
  //   $this->setRawAttributes(array_merge($this->attributes, array(
  //     'uuid_presupuesto' => Capsule::raw("ORDER_UUID(uuid())")
  //   )), true);
  //   parent::__construct($attributes);
  // }

  public function getCreatedAtAttribute($date){
    return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d H:i:s');
  }

	public function presupuesto()
	{
			return $this->belongsTo('Presupuesto_orm','presupuesto_id');
	}
	public function cuentas()
	{
		return $this->belongsTo('Cuentas_orm','cuentas_id');
	}

	public function transacciones_gastos($cuenta_id,$centro_id,$fecha){
		//enviar la fecha 01-2016
		return Transaccion_orm::where(function($query)use($cuenta_id,$centro_id,$fecha){
			$query->where('cuenta_id','=',$cuenta_id);
			$query->where('centro_id','=',$centro_id);
			$query->whereRaw('DATE_FORMAT(created_at,"%m-%Y") = ?',array($fecha));
		})->sum('debito');

	}

}
