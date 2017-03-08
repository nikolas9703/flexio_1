<?php 

namespace Flexio\Modulo\Polizas\Models;

use Illuminate\Database\Eloquent\Model as Model;

/**
* 
*/
class PolizasAcreedores_detalles extends Model
{
	
	protected $table        = 'pol_poliza_acreedores_detalles';    
    protected $fillable     = ['acreedor', 'porcentaje_cesion', 'monto_cesion', 'fecha_inicio', 'fecha_fin', 'id_poliza', 'updated_at', 'created_at', 'detalle_unico', 'idinteres_detalle'];
    protected $guarded      = ['id'];
}