<?php
namespace Flexio\Modulo\Polizas\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Usuarios\Models\Usuarios;

class PolizasProyecto extends Model
{
	
	protected $table = 'pol_poliza_proyecto'; 
	protected $fillable =[
		"id",
		"uuid_proyecto",
		"id_poliza",
		"empresa_id",
		"numero",
		"nombre_proyecto",
		"no_orden",
		"contratista",
		"representante_legal",
		"duracion",
		"fecha",
		"monto",
		"monto_afianzado",
		"acreedor",
		"porcentaje_acreedor",
		"ubicacion",
		"observaciones",
		"updated_at",
		"created_at",
		"estado",
		"tipo_id",
		"tipo_propuesta",
		"validez_fianza_pr",
		"tipo_fianza",
		"asignado_acreedor",
		"fecha_concurso",
		"acreedor_opcional",
		"validez_fianza_opcional",
		"tipo_propuesta_opcional",
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

	
	public static function listar_proyecto_provicional($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL, $id_poliza=NULL) 
	{

		$mainQuery['id_poliza'] =$id_poliza;
		 $mainQuery['id_poliza'] =$id_poliza;
        if(isset($clause['desde'])){
            if($clause['desde']=="renovar"){
                $mainQuery=[];
                $mainQuery['detalle_unico'] =$clause['detalleUnico'];
            }
        }
        $proyecto = self::where($mainQuery)->where(function($query) use($clause,$sidx,$sord,$limit,$start){     
        	if($limit!=NULL) $query->skip($start)->take($limit);           
        });

        if (isset($clause['no_orden']) && $clause['no_orden'] != "" ) {
            $proyecto->where("no_orden", "LIKE", "%".$clause['no_orden']."%");
        }
        if (isset($clause['nombre_proyecto']) && $clause['nombre_proyecto'] != "" ) {
            $proyecto->where("nombre_proyecto", "LIKE", "%".$clause['nombre_proyecto']."%");
        }
        if (isset($clause['ubicacion']) && $clause['ubicacion'] != "" ) {
            $proyecto->where("ubicacion", "LIKE", "%".$clause['ubicacion']."%");
        }
        
        if($sidx!=NULL && $sord!=NULL){ $proyecto->orderBy($sidx, $sord); }

        return $proyecto->get();
    }

    public function listar_intereses_asegurados($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){

        $proyecto = self::select('pol_poliza_proyecto.*','usuarios.nombre','usuarios.apellido')->join('usuarios','usuarios.id','=','pol_poliza_proyecto.creado_por')->where('id_poliza',$clause['id_poliza'])->where(function($query) use($clause,$sidx,$sord,$limit,$start){
            if($limit!=NULL) $query->skip($start)->take($limit);
        });
        if($sidx!=NULL && $sord!=NULL) $proyecto->orderBy($sidx, $sord);
        return $proyecto->get();
    }

    public function usuario(){
        $this->hasOne(Usuarios::class, 'id', 'creado_por');
    }

}