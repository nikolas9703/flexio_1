<?php
namespace Flexio\Modulo\Reclamos\Models;

use Illuminate\Database\Eloquent\Model as Model;

class ReclamosProyecto extends Model
{
	
	protected $table = 'rec_reclamos_proyecto'; 
	protected $fillable =[
		"id",
		"uuid_proyecto",
		"id_reclamo",
		"empresa_id",
		"numero",
		"nombre_proyecto",
		"no_orden",
		"contratista",
		"representante_legal",
		"duracion",
		"fecha",
		"monto",
		"monto_afianzado",
		"acreedor",
		"porcentaje_acreedor",
		"ubicacion",
		"observaciones",
		"updated_at",
		"created_at",
		"estado",
		"tipo_id",
		"tipo_propuesta",
		"validez_fianza_pr",
		"tipo_fianza",
		"asignado_acreedor",
		"fecha_concurso",
		"acreedor_opcional",
		"validez_fianza_opcional",
		"tipo_propuesta_opcional"
	]; 
	public $timestamps = false;



}