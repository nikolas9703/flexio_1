<?php
namespace Flexio\Modulo\Polizas\Models;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Agentes\Models\Agentes;

class SegPolizasAgentePrin extends Model
{
    protected $table        = 'pol_polizas_agente_prin';    
    protected $fillable     = ['agente_id', 'poliza_id', 'comision','created_at','updated_at'];
    protected $guarded      = ['id'];
    
    //scopes
    public function datosPoliza(){
        return $this->hasOne(Polizas::class, 'id', 'poliza_id');
    }
	public function datosAgente(){
        return $this->hasOne(Agentes::class, 'id', 'agente_id');
    }
}   