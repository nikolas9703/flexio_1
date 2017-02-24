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
        $articulo = self::join("seg_catalogo","seg_catalogo.id","=","pol_poliza_articulo.id_condicion")->where("id_poliza", $id_poliza)->where(function($query) use($clause,$sidx,$sord,$limit,$start){ 
        	if($limit!=NULL) $query->skip($start)->take($limit);           
        });

        if (isset($clause['nombre']) && $clause['nombre'] != "" ) {
        	$articulo->where("nombre", "LIKE", "%".$clause['nombre']."%");
        }
        if (isset($clause['modelo']) && $clause['modelo'] != "" ) {
        	$articulo->where("modelo", "LIKE", "%".$clause['modelo']."%");
        }
        if (isset($clause['numero_serie']) && $clause['numero_serie'] != "" ) {
        	$articulo->where("numero_serie", "LIKE", "%".$clause['numero_serie']."%");
        }

        $articulo->select("pol_poliza_articulo.estado", "pol_poliza_articulo.id", "pol_poliza_articulo.id_poliza", "pol_poliza_articulo.numero", "pol_poliza_articulo.nombre", "pol_poliza_articulo.clase_equipo", "pol_poliza_articulo.marca", "pol_poliza_articulo.modelo", "pol_poliza_articulo.anio", "pol_poliza_articulo.numero_serie", "pol_poliza_articulo.valor", "pol_poliza_articulo.fecha_inclusion", "seg_catalogo.etiqueta as id_condicion");
        
        if($sidx!=NULL && $sord!=NULL){ $articulo->orderBy($sidx, $sord); }

        return $articulo->get();
    }
}