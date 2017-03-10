<?php
namespace Flexio\Modulo\ComisionesSeguros\Models;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Agentes\Models\Agentes;
use Flexio\Modulo\Polizas\Models\Polizas;

class SegComisionesParticipacion extends Model
{
    protected $table        = 'seg_comisiones_participacion';    
    protected $fillable     = ['agente_id', 'porcentaje', 'monto','no_recibo','comision_id','created_at','updated_at','fecha_pago'];
    protected $guarded      = ['id'];
    
    //scopes
    public function datosPoliza(){
        return $this->hasOne(Polizas::class, 'id', 'poliza_id');
    }
	public function datosAgente(){
        return $this->hasOne(Agentes::class, 'id', 'agente_id');
    }
	public function datosComision(){
        return $this->hasOne(ComisionesSeguros::class, 'id', 'comision_id');
    }
}   