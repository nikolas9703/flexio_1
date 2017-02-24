<?php
namespace Flexio\Modulo\Polizas\Models;

use Illuminate\Database\Eloquent\Model as Model;

class PolizasProyecto extends Model
{
	
	protected $table = 'pol_poliza_proyecto'; 
	protected $fillable =[
		"id",
		"uuid_proyecto",
		"id_poliza",
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
		"tipo_propuesta_opcional",
		"detalle_certificado",
		"detalle_suma_asegurada",
		"detalle_prima",
		"detalle_deducible",
		"fecha_inclusion"
	]; 
	public $timestamps = false;

	
	public static function listar_proyecto_provicional($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL, $id_poliza=NULL) {
        $proyecto = self::where("id_poliza", $id_poliza)->where(function($query) use($clause,$sidx,$sord,$limit,$start){     
        	if($limit!=NULL) $query->skip($start)->take($limit);           
        });
        
        if($sidx!=NULL && $sord!=NULL){ $proyecto->orderBy($sidx, $sord); }

        return $proyecto->get();
    }

}