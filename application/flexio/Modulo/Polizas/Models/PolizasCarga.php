<?php
namespace Flexio\Modulo\Polizas\Models;

use Illuminate\Database\Eloquent\Model as Model;

class PolizasCarga extends Model
{
	
	protected $table = 'pol_poliza_carga'; 
	protected $fillable =["id","empresa_id","id_poliza","numero","detalle","no_liquidacion","fecha_despacho","fecha_arribo","valor","tipo_empaque","condicion_envio","medio_transporte","origen","destino","observaciones","updated_at","created_at","tipo_id","tipo_obligacion","acreedor","estado","acreedor_opcional","tipo_obligacion_opcional","detalle_certificado","detalle_suma_asegurada","detalle_prima","detalle_deducible","fecha_inclusion"]; 
	public $timestamps = false;

	public static function listar_carga_provicional($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL, $id_poliza=NULL) {
        $carga = self::where("id_poliza", $id_poliza)->where(function($query) use($clause,$sidx,$sord,$limit,$start){      
        	if($limit!=NULL) $query->skip($start)->take($limit);          
        });
        
        if($sidx!=NULL && $sord!=NULL){ $carga->orderBy($sidx, $sord); }

        return $carga->get();
    }
	
}