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
        $maritimo = self::select("pol_poliza_maritimo.*", "pro_proveedores.nombre")->where("id_poliza", $id_poliza)->leftjoin("pro_proveedores","pro_proveedores.id","=","pol_poliza_maritimo.acreedor")->where(function($query) use($clause,$sidx,$sord,$limit,$start){               
        	if($limit!=NULL) $query->skip($start)->take($limit); 
        });
        
        if($sidx!=NULL && $sord!=NULL){ $maritimo->orderBy($sidx, $sord); }

        return $maritimo->get();
    }
	

}