<?php
namespace Flexio\Modulo\Polizas\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Usuarios\Models\Usuarios;

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
	"fecha_inclusion",
	"fecha_exclusion",
	"creado_por",
	"detalle_unico",
	"id_interes",
	"detalle_unico",
	"id_interes"
	]; 
	public $timestamps = false;
	protected $guarded      = ['id'];

	
	public static function listar_articulo_provicional($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL, $id_poliza=NULL)
	{
		$mainQuery['id_poliza'] =$id_poliza;
        if(isset($clause['desde'])){
            if($clause['desde']=="renovar"){
                $mainQuery=[];
                $mainQuery['detalle_unico'] =$clause['detalleUnico'];
            }
        }
		$articulo = self::leftJoin("seg_catalogo","seg_catalogo.id","=","pol_poliza_articulo.id_condicion")->where($mainQuery)->where(function($query) use($clause,$sidx,$sord,$limit,$start){ 
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

		$articulo->select("pol_poliza_articulo.estado", "pol_poliza_articulo.id", "pol_poliza_articulo.id_poliza", "pol_poliza_articulo.numero", "pol_poliza_articulo.nombre", "pol_poliza_articulo.clase_equipo", "pol_poliza_articulo.marca", "pol_poliza_articulo.modelo", "pol_poliza_articulo.anio", "pol_poliza_articulo.numero_serie", "pol_poliza_articulo.valor", "pol_poliza_articulo.fecha_inclusion", "seg_catalogo.etiqueta as id_condicion","pol_poliza_articulo.id_interes");
		
		if($sidx!=NULL && $sord!=NULL){ $articulo->orderBy($sidx, $sord); }

		return $articulo->get();
	}

	public function listar_intereses_asegurados($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){

		$articulo = self::select('pol_poliza_articulo.*','usuarios.nombre','usuarios.apellido')->join('usuarios','usuarios.id','=','pol_poliza_articulo.creado_por')->where('id_poliza',$clause['id_poliza'])->where(function($query) use($clause,$sidx,$sord,$limit,$start){
			if($limit!=NULL) $query->skip($start)->take($limit);
		});
		if($sidx!=NULL && $sord!=NULL) $articulo->orderBy($sidx, $sord);

		return $articulo->get();
	}

	public function usuario(){
		$this->hasOne(Usuarios::class, 'id', 'creado_por');
	}

}