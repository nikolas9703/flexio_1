<?php
namespace Flexio\Modulo\Solicitudes\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Solicitudes\Models\Solicitudes;

class SolicitudesIntereses extends Model
{
    protected $table        = 'seg_solicitudes_intereses';    
    protected $fillable     = ['id_solicitudes', 'id_intereses', 'detalle_prima', 'detalle_beneficio', 'detalle_relacion', 'detalle_monto', 'detalle_int_asociado'];
    protected $guarded      = ['id'];
    
    //scopes
    public function solicitudes() {
        return $this->hasOne(Solicitudes::class, 'id', 'id_solicitudes');
    }
    
}