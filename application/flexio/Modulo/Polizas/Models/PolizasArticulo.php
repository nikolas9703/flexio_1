<?php
namespace Flexio\Modulo\Polizas\Models;

use Illuminate\Database\Eloquent\Model as Model;

class PolizasArticulo extends Model
{
	
	protected $table = 'pol_poliza_articulo'; 
	protected $fillable =[
		"uuid_articulo",
		"id_poliza",
		"numero",
		"empresa_id",
		"nombre",
		"clase_equipo",
		"marca",
		"modelo",
		"anio",
		"numero_serie",
		"id_condicion",
		"valor",
		"observaciones",
		"estado",
		"detalle_certificado",
		"detalle_suma_asegurada",
		"detalle_prima",
		"detalle_deducible",
		"updated_at",
		"created_at",
		"fecha_inclusion"
		]; 
	public $timestamps = false;
	protected $guarded      = ['id'];

	
	public static function listar_articulo_provicional($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL, $id_poliza=NULL) {
        $articulo = self::where("id_poliza", $id_poliza)->where(function($query) use($clause,$sidx,$sord,$limit,$start){     
       	 if($limit!=NULL) $query->skip($start)->take($limit);           
        });
        
        if($sidx!=NULL && $sord!=NULL){ $articulo->orderBy($sidx, $sord); }

        return $articulo->get();
    }
}