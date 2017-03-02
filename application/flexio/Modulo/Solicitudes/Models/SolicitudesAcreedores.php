<?php 

namespace Flexio\Modulo\Solicitudes\Models;

use Illuminate\Database\Eloquent\Model as Model;

/**
* 
*/
class SolicitudesAcreedores extends Model
{
	
	protected $table        = 'seg_solicitudes_acreedores';    
    protected $fillable     = ['acreedor', 'porcentaje_cesion', 'monto_cesion', 'fecha_inicio', 'fecha_fin', 'id_solicitud', 'updated_at', 'created_at'];
    protected $guarded      = ['id'];
}