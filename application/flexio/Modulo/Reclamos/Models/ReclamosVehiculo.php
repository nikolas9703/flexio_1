<?php
namespace Flexio\Modulo\Reclamos\Models;

use Illuminate\Database\Eloquent\Model as Model;

class ReclamosVehiculo extends Model
{
	
	protected $table = 'rec_reclamos_vehiculo'; 
	protected $fillable =[
		"id",
		"id_reclamo",
		"numero",
		"chasis",
		"uuid_vehiculo",
		"unidad",
		"marca",
		"modelo",
		"placa",
		"ano",
		"motor",
		"color",
		"capacidad",
		"uso",
		"condicion",
		"operador",
		"extras",
		"valor_extras",
		"acreedor",
		"porcentaje_acreedor",
		"observaciones",
		"empresa_id",
		"created_at",
		"updated_at",
		"estado"
	]; 
	public $timestamps = false;
	

}