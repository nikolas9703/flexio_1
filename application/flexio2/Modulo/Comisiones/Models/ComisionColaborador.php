<?php
namespace Flexio\Modulo\Comisiones\Models;
use Illuminate\Database\Capsule\Manager as Capsule;
use \Illuminate\Database\Eloquent\Model as Model;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\Colaboradores\Models\Colaboradores;
use Flexio\Modulo\ConfiguracionPlanilla\Models\Deducciones;
use Flexio\Modulo\ConfiguracionPlanilla\Models\Acumulados;

class ComisionColaborador extends Model
{
	protected $table = 'com_colaboradores';
	protected $fillable = ['comision_id', 'colaborador_id','descripcion','monto_total','descuento', 'estado' ];
	protected $guarded = ['id'];
  protected $appends = ['monto_deducido', 'monto_neto'];

	public function colaborador(){
		return $this->hasOne(Colaboradores::Class, 'id', 'colaborador_id');
	}

	public function deducciones_aplicados(){
		return $this->hasMany(ComisionColaboradorDeduccion::Class,'com_colaborador_id','id');
	}

	public function acumulados_aplicados(){
		return $this->hasMany(ComisionColaboradorAcumulado::Class,'com_colaborador_id','id');
 	}

	public function pago_extraordinario(){
	    return $this->belongsTo(Comisiones::Class, 'comision_id');
	}

	public function getMontoDeducidoAttribute() {
 			$monto_deducido  = $this->deducciones_aplicados()->sum('monto');
 			return (float) $monto_deducido;
 	}

	public function getMontoNetoAttribute() {
			$monto_neto = $this->monto_total - $this->monto_deducido;
 			return (float) $monto_neto;
 	}
	public function scopeDeComision($query, $comision_id) {
		return $query->where("comision_id", $comision_id);
	}
}
