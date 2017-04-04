<?php
namespace Flexio\Modulo\Polizas\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Usuarios\Models\Usuarios;

class PolizasAereo extends Model
{
	
	protected $table = 'pol_poliza_aereo'; 
	protected $fillable =[
	"id",
	"empresa_id",
	"id_poliza",
	"serie","marca",
	"modelo",
	"matricula",
	"valor",
	"pasajeros",
	"tripulacion",
	"observaciones",
	"updated_at",
	"created_at",
	"numero",
	"tipo_id",
	"detalle_certificado",
	"detalle_suma_asegurada",
	"detalle_prima",
	"detalle_deducible",
	"estado",
	"fecha_inclusion",
    "fecha_exclusion",
	"creado_por",
	"id_interes",
	"detalle_unico"]; 
	public $timestamps = false;

	public static function listar_aereo_provicional($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL, $id_poliza=NULL) {

		 $mainQuery['id_poliza'] =$id_poliza;
        if(isset($clause['desde'])){
            if($clause['desde']=="renovar"){
                $mainQuery=[];
                $mainQuery['detalle_unico'] =$clause['detalleUnico'];
            }
        }
        $aereo = self::where($mainQuery)->where(function($query) use($clause,$sidx,$sord,$limit,$start){ 
        	if($limit!=NULL) $query->skip($start)->take($limit);               
        });

        if (isset($clause['matricula']) && $clause['matricula'] != "" ) {
        	$aereo->where("matricula", "LIKE", "%".$clause['matricula']."%");
        }
        if (isset($clause['modelo']) && $clause['modelo'] != "" ) {
        	$aereo->where("modelo", "LIKE", "%".$clause['modelo']."%");
        }
        if (isset($clause['serie']) && $clause['serie'] != "" ) {
        	$aereo->where("serie", "LIKE", "%".$clause['serie']."%");
        }
        
        if($sidx!=NULL && $sord!=NULL){ $aereo->orderBy($sidx, $sord); }

        return $aereo->get();
    }

    public function listar_intereses_asegurados($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){

        $aereo = self::select('pol_poliza_aereo.*','usuarios.nombre','usuarios.apellido')->join('usuarios','usuarios.id','=','pol_poliza_aereo.creado_por')->where('id_poliza',$clause['id_poliza'])->where(function($query) use($clause,$sidx,$sord,$limit,$start){
            if($limit!=NULL) $query->skip($start)->take($limit);
        });
        if($sidx!=NULL && $sord!=NULL)$aereo->orderBy($sidx, $sord);
        return $aereo->get();
    }
	
    public function usuario(){
        $this->hasOne(Usuarios::class, 'id', 'creado_por');
    }
}