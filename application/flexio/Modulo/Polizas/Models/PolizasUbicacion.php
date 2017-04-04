<?php
namespace Flexio\Modulo\Polizas\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Usuarios\Models\Usuarios;

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
	"fecha_inclusion",
	"fecha_exclusion",
	"creado_por",
	"detalle_unico",
	"id_interes"
	]; 
	public $timestamps = false;

	
	public static function listar_ubicacion_provicional($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL, $id_poliza=NULL) {
		if((isset($clause['acreedor'])) && ($clause['acreedor']!='otro' && $clause['acreedor']!='' )) {
			$mainQuery['id_poliza'] =$id_poliza;
			if(isset($clause['desde'])){
				if($clause['desde']=="renovar"){
					$mainQuery=[];
					$mainQuery['detalle_unico'] =$clause['detalleUnico'];
				}
			}
			$ubicacion = self::select("pol_poliza_ubicacion.*","pro_proveedores.nombre as acreedor")->leftJoin("pro_proveedores","pro_proveedores.id","=","pol_poliza_ubicacion.acreedor")->where("pro_proveedores.tipo_id","=", $clause['acreedor'])->where($mainQuery)->where(function($query) use($clause,$sidx,$sord,$limit,$start){

				if($limit!=NULL) $query->skip($start)->take($limit);      
			});


		} else {
			$ubicacion = self::select("pol_poliza_ubicacion.*")->where("id_poliza", $id_poliza)->where(function($query) use($clause,$sidx,$sord,$limit,$start){

				if($limit!=NULL) $query->skip($start)->take($limit);   	      
			});


		}

		if (isset($clause['nombre']) && $clause['nombre'] != "" ) {
			$ubicacion->where("nombre", "LIKE", "%".$clause['nombre']."%");
		}
		if (isset($clause['direccion']) && $clause['direccion'] != "" ) {
			$ubicacion->where("direccion", "LIKE", "%".$clause['direccion']."%");
		}

		if($sidx!=NULL && $sord!=NULL){ $ubicacion->orderBy($sidx, $sord); }
		return $ubicacion->get();

	}

	public function listar_intereses_asegurados($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){

		$ubicacion = self::select('pol_poliza_ubicacion.*','usuarios.nombre','usuarios.apellido')->join('usuarios','usuarios.id','=','pol_poliza_ubicacion.creado_por')->where('id_poliza',$clause['id_poliza'])->where(function($query) use($clause,$sidx,$sord,$limit,$start){
			if($limit!=NULL) $query->skip($start)->take($limit);
		});
		if($sidx!=NULL && $sord!=NULL) $ubicacion->orderBy($sidx, $sord);

		return $ubicacion->get();
	}

	public function usuario(){
		$this->hasOne(Usuarios::class, 'id', 'creado_por');
	}
}