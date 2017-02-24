<?php 

namespace Flexio\Modulo\Solicitudes\Models;

use Illuminate\Database\Eloquent\Model as Model;

/**
* 
*/
class SolicitudesDeduccion extends Model
{
	
	protected $table = 'seg_solicitudes_deduccion'; 
	protected $fillable =["deduccion","valor_deduccion","id_solicitud"]; 
	public $timestamps = false;
}