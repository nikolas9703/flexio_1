<?php
namespace Flexio\Modulo\Reclamos\Models;

use Illuminate\Database\Eloquent\Model as Model;

class ReclamosUbicacion extends Model
{
	
	protected $table = 'rec_reclamos_ubicacion'; 
	protected $fillable =[
		"id",
		"uuid_ubicacion",
		"empresa_id",
		"id_reclamo",
		"numero",
		"nombre",
		"direccion",
		"edif_mejoras",
		"contenido",
		"maquinaria",
		"inventario",
		"acreedor",
		"porcentaje_acreedor",
		"observaciones",
		"estado",
		"tipo_id",
		"updated_at",
		"created_at",
		"acreedor_opcional"
	]; 
	public $timestamps = false;

}