<?php

namespace Flexio\Modulo\Presupuesto\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

use Flexio\Modulo\Contabilidad\Models\Cuentas;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable;
use Flexio\Modulo\CentrosContables\Models\CentrosContables;

class CentroCuentaPresupuesto extends Model
{
	protected $table = 'pres_presupuesto_cuenta_centro';
	protected $fillable = ['presupuesto_id','cuentas_id','empresa_id','centro_contable_id','montos','info_presupuesto','usuario_id','porcentaje'];
	protected $guarded = ['id'];

	public static function boot(){
		parent::boot();

		static::updating(function($cuentaPresupuesto){
			$data = $cuentaPresupuesto->fresh();
			$created =[
				'antes' => json_encode(array_intersect_key($data->toArray(),$cuentaPresupuesto->getDirty())),
				'despues' => json_encode($cuentaPresupuesto->getDirty()),
				'codigo' => $cuentaPresupuesto->presupuesto->codigo,
				'usuario_id' => $data->usuario_id,
				'empresa_id' => $data->empresa_id,
				'presupuesto_id'=> $data->presupuesto_id,
				'descripcion' => 'Actualizó el presupuesto',
				'tipo'=> 'actualizado',
				'codigo_cuenta'=> $data->cuentas->codigo
			];
			PresupuestoHistorial::create($created);
		});
	}


    public function getCreatedAtAttribute($date){
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d H:i:s');
    }

	public function presupuesto()
	{
		return $this->belongsTo(Presupuesto::class,'presupuesto_id');
	}
	public function cuentas()
	{
		return $this->belongsTo(Cuentas::class,'cuentas_id');
	}

	public function centro_contable(){
		return $this->belongsTo(CentrosContables::class,'centro_contable_id');
	}

	public function transacciones_gastos($cuenta_id,$centro_id,$fecha){
		//enviar la fecha 01-2016
		$transacciones = AsientoContable::where(function($query)use($cuenta_id,$centro_id,$fecha){
			$query->where('cuenta_id','=',$cuenta_id);
			$query->where('centro_id','=',$centro_id);
			$query->whereRaw('DATE_FORMAT(created_at,"%m-%Y") = ?',array($fecha));
		})->get()->sum(function($query){
			return $query->debito +  $query->credito;
		});

		return $transacciones;

	}

	public function presupuesto_por_avance(){
		return ($this->montos * $this->porcentaje) / 100;
	}

	public function comprometido(){
		return new Comprometido($this);
	}

	public function gastado(){
		return new Gastado($this);
	}

	public function teorico(){
		if($this->presupuesto_por_avance() == 0){
			return 0;
		}
		return ($this->comprometido()->total() / $this->presupuesto_por_avance()) * 100;
	}
}
