<?php
namespace Flexio\Modulo\Vacaciones\Models;

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Colaboradores\Models\Colaboradores;
use Flexio\Modulo\Planilla\Models\Planilla;


class Vacaciones extends Model
{
  protected $table = 'vac_vacaciones';
	protected $fillable = ['empresa_id', 'colaborador_id', 'dias_disponibles', 'fecha_desde', 'fecha_hasta', 'fecha_pago', 'cantidad_dias', 'estado_id', 'pago_inmediato_id', 'cuenta_pasivo_id', 'observaciones', 'archivo_ruta', 'archivo_nombre', 'creado_por'];
	protected $guarded = ['id'];



	public function colaborador(){
		return $this->hasOne(Colaboradores::Class, 'id', 'colaborador_id');
	}
  public function planilla(){
    return $this->belongsToMany(Planilla::Class, 'pln_planilla_vacacion', 'vacacion_id', 'planilla_id');
  }

}
