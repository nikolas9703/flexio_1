<?php

namespace Flexio\Modulo\Reclamos\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Usuarios\Models\Usuarios;
use Flexio\Modulo\Reclamos\Models\Reclamos;

class ReclamosDocumentacion extends Model {

    protected $table = 'rec_reclamos_documentos';
    protected $fillable = ['id_reclamo', 'valor'];
    protected $guarded = ['id'];

    //scopes
    public function scopeDeEmpresa($query, $empresa_id) {
        return $query->where("empresa_id", $empresa_id);
    }

    public function creadopor() {
        return $this->hasOne(Usuarios::class, 'id', 'creado_por');
    }
    public function reclamos() {
        return $this->hasOne(Solicitudes::class, 'id', 'id_reclamo');
    }
    

}