<?php

namespace Flexio\Modulo\Solicitudes\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Carbon\Carbon as Carbon;

/**
 * 
 */
class SolicitudesBitacora extends Model {

    protected $table = 'seg_solicitudes_bitacora';
    protected $fillable = ['comentario', 'comentable_id', 'comentable_type', 'usuario_id', 'created_at', 'updated_at', 'empresa_id'];
    protected $guarded = ['id'];

    public function getCuantoTiempo($created_at) {
        return Carbon::createFromFormat('Y-m-d H:i:s', $created_at)->diffForHumans();
    }

    public function getFechaCreacion($created_at) {
        return Carbon::createFromFormat('Y-m-d H:i:s', $created_at)->formatLocalized('%d de %B');
    }

    public function getHora($created_at) {
        return Carbon::createFromFormat('Y-m-d H:i:s', $created_at)->format('h:i a');
    }

}
