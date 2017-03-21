<?php 

namespace Flexio\Modulo\Reclamos\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Reclamos\Models\ReclamosPersonas;
use Flexio\Modulo\Polizas\Models\PolizasPersonas;
use Flexio\Modulo\Reclamos\Models\Reclamos;

/**
* 
*/
class ReclamosDetalleSalud extends Model
{
	
	protected $table = 'rec_reclamos_detalle_salud'; 
	protected $fillable =["tipo_salud","hospital","especialidad_salud", "doctor", "detalle_salud", "fecha_salud", "monto_salud", "detalle_unico", "id_reclamo", "id_int_pol"];
	protected $guarded = ['id'];
	public $timestamps = false; 


	public static function listar_salud_provicional($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL, $id_poliza=NULL) {

		$idsinteres = array();
		//Se obtienen Todos los ids de Principal y Dependientes de elegido
		$interes = PolizasPersonas::where("id", $clause['id_interes'])->where("id_poliza", $clause['id_poliza'])->first();
		if (empty($interes)) {
			$idint = 0;
			$detaso = -1;
			$id = 0;
		}else{
			$id = $interes->id;
			$idint = $interes->id_interes;
			$detaso = $interes->detalle_int_asociado;
		}
		array_push($idsinteres, $id);
		if ($detaso == 0) {
			$interes2 = PolizasPersonas::where("detalle_int_asociado", $idint)->where("id_poliza", $clause['id_poliza'])->get();
			foreach ($interes2 as $value) {
				array_push($idsinteres, $value->id);
			}
		}else if($detaso != -1){
			array_push($idsinteres, $detaso);
			$interes2 = PolizasPersonas::where("detalle_int_asociado", $idint)->where("id_poliza", $clause['id_poliza'])->get();
			foreach ($interes2 as $value) {
				array_push($idsinteres, $value->id);
			}
		}	
		//----------------------------------------------------------------------
		//Se obtienen todos los reclamos de los intereses
		$idsreclamos = array(0);
		$reclamos = Reclamos::whereIn("id_interes_asegurado", $idsinteres)->where("tipo_interes", 5)->get();
		foreach ($reclamos as $value) {
			array_push($idsreclamos, $value->id);
		}
		//----------------------------------------------------------------------
		//Se obtienen todos los reclamos asociados
		$salud = self::leftJoin("rec_reclamos","rec_reclamos.id","=","rec_reclamos_detalle_salud.id_reclamo")->join("seg_catalogo", "seg_catalogo.id", "=", "rec_reclamos_detalle_salud.tipo_salud")->leftJoin("rec_reclamos_personas","rec_reclamos_personas.id_reclamo","=","rec_reclamos.id")->leftJoin("pol_poliza_personas","pol_poliza_personas.id","=","rec_reclamos_detalle_salud.id_int_pol")->where(function ($query) use($clause, $idsreclamos){
			$query->whereIn('rec_reclamos_detalle_salud.id_reclamo', $idsreclamos);
	        $query->orWhere('rec_reclamos_detalle_salud.detalle_unico', $clause['detalle_unico']);	        
	    });
		//----------------------------------------------------------------------
        $salud->select("seg_catalogo.etiqueta", "rec_reclamos_detalle_salud.*", "rec_reclamos.numero", "rec_reclamos.no_certificado", "rec_reclamos.numero_caso", "rec_reclamos_personas.nombrePersona", "rec_reclamos_personas.detalle_certificado", "pol_poliza_personas.nombrePersona as nombrePersonaP");

        $salud->where(function($query) use($clause,$sidx,$sord,$limit,$start){      
        	if($limit!=NULL) $query->skip($start)->take($limit);          
        });
        if($sidx!=NULL && $sord!=NULL){ $salud->orderBy($sidx, $sord); }

        return $salud->get();
    }
}