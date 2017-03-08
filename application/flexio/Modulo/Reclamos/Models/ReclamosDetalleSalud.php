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
	protected $fillable =["tipo_salud","hospital","especialidad_salud", "doctor", "detalle_salud", "fecha_salud", "monto_salud", "detalle_unico", "id_reclamo"];
	protected $guarded = ['id'];
	public $timestamps = false; 


	public static function listar_salud_provicional($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL, $id_poliza=NULL) {

		$idsinteres = array();
		$interes = PolizasPersonas::where("id", $clause['id_interes'])->first();
		if (empty($interes)) {
			$idint = 0;
			$detaso = -1;
		}else{
			$idint = $interes->id;
			$detaso = $interes->detalle_int_asociado;
		}
		array_push($idsinteres, $idint);
		if ($detaso == 0) {
			$interes2 = PolizasPersonas::where("detalle_int_asociado", $idint)->get();
			foreach ($interes2 as $value) {
				array_push($idsinteres, $value->id);
			}
		}		
		$idsreclamos = array();
		$reclamos = Reclamos::whereIn("id_interes_asegurado", $idsinteres)->where("tipo_interes", 5)->get();
		foreach ($reclamos as $value) {
			array_push($idsreclamos, $value->id);
		}

        $salud = self::where(function($query) use($clause,$sidx,$sord,$limit,$start){      
        	if($limit!=NULL) $query->skip($start)->take($limit);          
        });

        /*$salud->where(function ($query) {
        	$query->whereIn('id_reclamo', $idsreclamos)->orWhere('detalle_unico', '=', $clause['detalle_unico']);
        });*/

        /*if (isset($clause['id_interes']) && $clause['id_interes'] != "" && $clause['id_interes'] != 0 ) {
        	$salud->where("id_interes", "=", $clause['id_interes']);
        }*/
        $salud->where(function ($query) use($clause, $idsreclamos){
	        	$query->whereIn('id_reclamos', $idsreclamos)->orWhere('detalle_unico', '=', $clause['detalle_unico']);
	        });
        /*if (isset($clause['medio_transporte']) && $clause['medio_transporte'] != "" ) {
        	$salud->where("mod_catalogos.etiqueta", "LIKE", "%".$clause['medio_transporte']."%");
        }*/

        /*$salud->select("pol_poliza_carga.estado as estado", "pol_poliza_carga.id as id", "pol_poliza_carga.id_poliza", "pol_poliza_carga.numero", "pol_poliza_carga.no_liquidacion", "pol_poliza_carga.fecha_arribo", "pol_poliza_carga.fecha_despacho", "mod_catalogos.etiqueta as medio_transporte", "pol_poliza_carga.valor", "pol_poliza_carga.origen", "pol_poliza_carga.destino", "pol_poliza_carga.fecha_inclusion");*/
        
        if($sidx!=NULL && $sord!=NULL){ $salud->orderBy($sidx, $sord); }

        return $salud->get();
    }
}