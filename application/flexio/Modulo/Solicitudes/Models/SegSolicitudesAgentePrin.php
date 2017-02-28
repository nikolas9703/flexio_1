<?php
namespace Flexio\Modulo\Solicitudes\Models;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Agentes\Models\Agentes;

class SegSolicitudesAgentePrin extends Model
{
    protected $table        = 'seg_solicitudes_agente_prin';    
    protected $fillable     = ['agente_id', 'solicitud_id', 'comision'];
    protected $guarded      = ['id'];
    
    //scopes
    public function datosSolicitud(){
        return $this->hasOne(Solicitudes::class, 'id', 'solitud_id');
    }
	public function datosAgente(){
        return $this->hasOne(Agentes::class, 'id', 'solitud_id');
    }
}   