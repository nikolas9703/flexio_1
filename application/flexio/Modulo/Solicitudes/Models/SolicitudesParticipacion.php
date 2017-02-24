<?php

namespace Flexio\Modulo\Solicitudes\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Usuarios\Models\Usuarios;
use Flexio\Modulo\Solicitudes\Models\Solicitudes;

class SolicitudesParticipacion extends Model {

    protected $table = 'seg_solicitudes_participacion';
    protected $fillable = ['id_solicitud', 'agente', 'porcentaje_participacion','documentos_tramites', 'updated_at', 'created_at'];
    protected $guarded = ['id'];

    //scopes
    public function scopeDeEmpresa($query, $empresa_id) {
        return $query->where("empresa_id", $empresa_id);
    }

    public function creadopor() {
        return $this->hasOne(Usuarios::class, 'id', 'creado_por');
    }
    public function solicitudes() {
        return $this->hasOne(Solicitudes::class, 'id', 'id_solicitud');
    }
    

}