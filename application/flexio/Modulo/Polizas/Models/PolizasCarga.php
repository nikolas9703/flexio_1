<?php
namespace Flexio\Modulo\Polizas\Models;

use Illuminate\Database\Eloquent\Model as Model;

class PolizasCarga extends Model
{
	
	protected $table = 'pol_poliza_carga'; 
	protected $fillable =["id","empresa_id","id_poliza","numero","detalle","no_liquidacion","fecha_despacho","fecha_arribo","valor","tipo_empaque","condicion_envio","medio_transporte","origen","destino","observaciones","updated_at","created_at","tipo_id","tipo_obligacion","acreedor","estado","acreedor_opcional","tipo_obligacion_opcional","detalle_certificado","detalle_suma_asegurada","detalle_prima","detalle_deducible","fecha_inclusion"]; 
	public $timestamps = false;

	public static function listar_carga_provicional($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL, $id_poliza=NULL) {
        $carga = self::join("mod_catalogos","mod_catalogos.id_cat","=","pol_poliza_carga.medio_transporte")->where("pol_poliza_carga.id_poliza", $id_poliza)->where(function($query) use($clause,$sidx,$sord,$limit,$start){      
        	if($limit!=NULL) $query->skip($start)->take($limit);          
        });

        if (isset($clause['no_liquidacion']) && $clause['no_liquidacion'] != "" ) {
        	$carga->where("pol_poliza_carga.no_liquidacion", "LIKE", "%".$clause['no_liquidacion']."%");
        }
        if (isset($clause['medio_transporte']) && $clause['medio_transporte'] != "" ) {
        	$carga->where("mod_catalogos.etiqueta", "LIKE", "%".$clause['medio_transporte']."%");
        }

        $carga->select("pol_poliza_carga.estado as estado", "pol_poliza_carga.id as id", "pol_poliza_carga.id_poliza", "pol_poliza_carga.numero", "pol_poliza_carga.no_liquidacion", "pol_poliza_carga.fecha_arribo", "pol_poliza_carga.fecha_despacho", "mod_catalogos.etiqueta as medio_transporte", "pol_poliza_carga.valor", "pol_poliza_carga.origen", "pol_poliza_carga.destino", "pol_poliza_carga.fecha_inclusion");
        
        if($sidx!=NULL && $sord!=NULL){ $carga->orderBy($sidx, $sord); }

        return $carga->get();
    }
	
}