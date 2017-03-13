<?php 

namespace Flexio\Modulo\Solicitudes\Models;

use Illuminate\Database\Eloquent\Model as Model;

/**
* 
*/
class SolicitudesAcreedores_detalles extends Model
{
	
	protected $table        = 'seg_solicitudes_acreedores_detalles';    
    protected $fillable     = ['acreedor', 'porcentaje_cesion', 'monto_cesion', 'fecha_inicio', 'fecha_fin', 'id_solicitud', 'updated_at', 'created_at', 'detalle_unico', 'idinteres_detalle'];
    protected $guarded      = ['id'];
}