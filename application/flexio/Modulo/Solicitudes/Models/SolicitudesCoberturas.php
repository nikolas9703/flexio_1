<?php 

namespace Flexio\Modulo\Solicitudes\Models;

use Illuminate\Database\Eloquent\Model as Model;

/**
* 
*/
class SolicitudesCoberturas extends Model
{
	
	protected $table = 'seg_solicitudes_coberturas'; 
	protected $fillable =["cobertura","valor_cobertura","id_solicitud"];
	public $timestamps = false; 
}