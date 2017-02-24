<?php
namespace Flexio\Modulo\Polizas\Models;

use Illuminate\Database\Eloquent\Model as Model;

class PolizasVehiculo extends Model
{
	
	protected $table = 'pol_poliza_vehiculo'; 
	protected $fillable =[
		"id",
		"id_poliza",
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
		"detalle_certificado",
		"detalle_suma_asegurada",
		"detalle_prima",
		"detalle_deducible",
		"estado",
		"fecha_inclusion"
	]; 
	public $timestamps = false;

	public static function listar_vehiculo_provicional($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL, $id_poliza =NULL) {
        $vehiculo = self::where("id_poliza", $id_poliza)->where(function($query) use($clause,$sidx,$sord,$limit,$start){
            
            if($limit!=NULL) $query->skip($start)->take($limit);            
        });

        if (isset($clause['chasis']) && $clause['chasis'] != "" ) {
            $vehiculo->where("chasis", "LIKE", "%".$clause['chasis']."%");
        }
        if (isset($clause['placa']) && $clause['placa'] != "" ) {
            $vehiculo->where("placa", "LIKE", "%".$clause['placa']."%");
        }
        if (isset($clause['operador']) && $clause['operador'] != "" ) {
            $vehiculo->where("operador", "LIKE", "%".$clause['operador']."%");
        }
        
        if($sidx!=NULL && $sord!=NULL){ $vehiculo->orderBy($sidx, $sord); }

        return $vehiculo->get();
    }
	

}