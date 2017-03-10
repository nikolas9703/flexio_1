<?php 

namespace Flexio\Modulo\Polizas\Models;

use Illuminate\Database\Eloquent\Model as Model;

/**
* 
*/
class PolizasAcreedores extends Model
{
	
	protected $table        = 'pol_polizas_acreedores';    
    protected $fillable     = ['acreedor', 'porcentaje_cesion', 'monto_cesion', 'id_poliza', 'updated_at', 'created_at', 'fecha_inicio', 'fecha_fin'];
    protected $guarded      = ['id'];
}