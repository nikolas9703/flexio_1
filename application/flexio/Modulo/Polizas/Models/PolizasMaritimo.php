<?php
namespace Flexio\Modulo\Polizas\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Proveedores\Models\Proveedores;

class PolizasMaritimo extends Model
{
	
	protected $table = 'pol_poliza_maritimo'; 
	protected $fillable =["id","uuid_casco_maritimo","empresa_id","id_poliza","numero","serie","nombre_embarcacion","tipo","marca","valor","pasajeros","acreedor","porcentaje_acreedor","observaciones","updated_at","created_at","tipo_id","detalle_certificado","detalle_suma_asegurada","detalle_prima","detalle_deducible","estado","fecha_inclusion"]; 
	public $timestamps = false;

	public function datosAcreedor() {
        return $this->hasOne(Proveedores::class, 'id', 'acreedor');
    }


	public static function listar_maritimo_provicional($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL, $id_poliza=NULL) {
        $maritimo = self::join("mod_catalogos","mod_catalogos.id_cat","=","pol_poliza_maritimo.tipo")->select("pol_poliza_maritimo.id", "pol_poliza_maritimo.numero", "pol_poliza_maritimo.nombre_embarcacion", "pol_poliza_maritimo.serie", "pol_poliza_maritimo.marca", "pol_poliza_maritimo.valor", "pol_poliza_maritimo.fecha_inclusion", "pol_poliza_maritimo.estado", "pro_proveedores.nombre", "mod_catalogos.etiqueta as tipo", "pol_poliza_maritimo.id_poliza")->where("pol_poliza_maritimo.id_poliza", $id_poliza)->leftjoin("pro_proveedores","pro_proveedores.id","=","pol_poliza_maritimo.acreedor")->where(function($query) use($clause,$sidx,$sord,$limit,$start){               
        	if($limit!=NULL) $query->skip($start)->take($limit); 
        });

        if (isset($clause['nombre_embarcacion']) && $clause['nombre_embarcacion'] != "" ) {
            $maritimo->where("nombre_embarcacion", "LIKE", "%".$clause['nombre_embarcacion']."%");
        }
        if (isset($clause['marca']) && $clause['marca'] != "" ) {
            $maritimo->where("marca", "LIKE", "%".$clause['marca']."%");
        }
        if (isset($clause['serie']) && $clause['serie'] != "" ) {
            $maritimo->where("serie", "LIKE", "%".$clause['serie']."%");
        }
        
        if($sidx!=NULL && $sord!=NULL){ $maritimo->orderBy($sidx, $sord); }

        return $maritimo->get();
    }
	

}