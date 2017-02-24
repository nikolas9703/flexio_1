<?php
namespace Flexio\Modulo\Polizas\Models;

use Illuminate\Database\Eloquent\Model as Model;

class PolizasAereo extends Model
{
	
	protected $table = 'pol_poliza_aereo'; 
	protected $fillable =["id","empresa_id","id_poliza","serie","marca","modelo","matricula","valor","pasajeros","tripulacion","observaciones","updated_at","created_at","numero","tipo_id","detalle_certificado","detalle_suma_asegurada","detalle_prima","detalle_deducible","estado","fecha_inclusion"]; 
	public $timestamps = false;

	public static function listar_aereo_provicional($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL, $id_poliza=NULL) {
        $aereo = self::where("id_poliza", $id_poliza)->where(function($query) use($clause,$sidx,$sord,$limit,$start){ 
        	if($limit!=NULL) $query->skip($start)->take($limit);               
        });
        
        if($sidx!=NULL && $sord!=NULL){ $aereo->orderBy($sidx, $sord); }

        return $aereo->get();
    }
	
}