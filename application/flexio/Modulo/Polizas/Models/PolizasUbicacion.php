<?php
namespace Flexio\Modulo\Polizas\Models;

use Illuminate\Database\Eloquent\Model as Model;

class PolizasUbicacion extends Model
{
	
	protected $table = 'pol_poliza_ubicacion'; 
	protected $fillable =[
		"id",
		"uuid_ubicacion",
		"empresa_id",
		"id_poliza",
		"numero",
		"nombre",
		"direccion",
		"edif_mejoras",
		"contenido",
		"maquinaria",
		"inventario",
		"acreedor",
		"porcentaje_acreedor",
		"observaciones",
		"estado",
		"tipo_id",
		"updated_at",
		"created_at",
		"acreedor_opcional",
		"detalle_certificado",
		"detalle_suma_asegurada",
		"detalle_prima",
		"detalle_deducible",
		"fecha_inclusion"
	]; 
	public $timestamps = false;

	
	public static function listar_ubicacion_provicional($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL, $id_poliza=NULL) {
		if((isset($clause['acreedor'])) && ($clause['acreedor']!='otro' || $clause['acreedor']!='' )) {
        $ubicacion = self::select("pol_poliza_ubicacion.*","pro_proveedores.nombre as acreedor")->leftjoin("pro_proveedores","pro_proveedores.id","=","pol_poliza_ubicacion.acreedor")->where("pro_proveedores.tipo_id","=", $clause['acreedor'])->where("id_poliza", $id_poliza)->where(function($query) use($clause,$sidx,$sord,$limit,$start){
            
        
          //$ubicacion = self::join("int_intereses_asegurados", "int_intereses_asegurados.interesestable_id", "=", "int_ubicacion.id")->join("int_intereses_asegurados_detalles", "int_intereses_asegurados_detalles.id_intereses", "=", "int_intereses_asegurados.id")->where("int_intereses_asegurados.interesestable_type", '7')->where(function($query) use($clause,$sidx,$sord,$limit,$start){  
              if($limit!=NULL) $query->skip($start)->take($limit);      
            });
        
        if($sidx!=NULL && $sord!=NULL){ $ubicacion->orderBy($sidx, $sord); }
        return $ubicacion->get();

        } else {
            $ubicacion = self::select("pol_poliza_ubicacion.*")->where("id_poliza", $id_poliza)->where(function($query) use($clause,$sidx,$sord,$limit,$start){
            
          //$ubicacion = self::join("int_intereses_asegurados", "int_intereses_asegurados.interesestable_id", "=", "int_ubicacion.id")->join("int_intereses_asegurados_detalles", "int_intereses_asegurados_detalles.id_intereses", "=", "int_intereses_asegurados.id")->where("int_intereses_asegurados.interesestable_type", '7')->where(function($query) use($clause,$sidx,$sord,$limit,$start){  
              if($limit!=NULL) $query->skip($start)->take($limit);   	      
            });
        
        if($sidx!=NULL && $sord!=NULL){ $ubicacion->orderBy($sidx, $sord); }
        return $ubicacion->get();
        }




       /* $articulo = self::select("pol_poliza_ubicacion.*","pro_proveedores.nombre as acreedor")->leftjoin("pro_proveedores","pro_proveedores.id","=","pol_poliza_ubicacion.acreedor")->where("id_poliza", $id_poliza)->where(function($query) use($clause,$sidx,$sord,$limit,$start){               
        });
        
        if($sidx!=NULL && $sord!=NULL){ $articulo->orderBy($sidx, $sord); }

        return $articulo->get();*/

    }

}