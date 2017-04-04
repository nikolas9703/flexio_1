<?php
namespace Flexio\Modulo\Polizas\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Proveedores\Models\Proveedores;
use Flexio\Modulo\Usuarios\Models\Usuarios;

class PolizasMaritimo extends Model
{
	
	protected $table = 'pol_poliza_maritimo'; 
	protected $fillable =[
    "id",
    "uuid_casco_maritimo",
    "empresa_id","id_poliza",
    "numero",
    "serie",
    "nombre_embarcacion",
    "tipo",
    "marca",
    "valor",
    "pasajeros",
    "acreedor",
    "porcentaje_acreedor",
    "observaciones",
    "updated_at",
    "created_at",
    "tipo_id",
    "detalle_certificado",
    "detalle_suma_asegurada",
    "detalle_prima",
    "detalle_deducible",
    "estado",
    "fecha_inclusion",
    "fecha_exclusion",
    "creado_por",
    "detalle_unico",
    "id_interes",
    "detalle_unico",
    "id_interes"
    ]; 
    public $timestamps = true;

    public function datosAcreedor() {
        return $this->hasOne(Proveedores::class, 'id', 'acreedor');
    }


    public static function listar_maritimo_provicional($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL, $id_poliza=NULL) {
        $mainQuery['id_poliza'] =$id_poliza;
        if(isset($clause['desde'])){
            if($clause['desde']=="renovar"){
                $mainQuery=[];
                $mainQuery['detalle_unico'] =$clause['detalleUnico'];
            }
        }
        $maritimo = self::leftJoin("mod_catalogos","mod_catalogos.id_cat","=","pol_poliza_maritimo.tipo")
        ->select("pol_poliza_maritimo.id", "pol_poliza_maritimo.numero", "pol_poliza_maritimo.nombre_embarcacion", "pol_poliza_maritimo.serie", "pol_poliza_maritimo.marca", "pol_poliza_maritimo.valor", "pol_poliza_maritimo.fecha_inclusion", "pol_poliza_maritimo.estado", "pro_proveedores.nombre", "mod_catalogos.etiqueta as tipo", "pol_poliza_maritimo.id_poliza","pol_poliza_maritimo.id_interes")
        ->where($mainQuery)->leftJoin("pro_proveedores","pro_proveedores.id","=","pol_poliza_maritimo.acreedor")->where(function($query) use($clause,$sidx,$sord,$limit,$start){               
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

    public function listar_intereses_asegurados($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){

        $maritimo = self::select('pol_poliza_maritimo.*','usuarios.nombre','usuarios.apellido')->join('usuarios','usuarios.id','=','pol_poliza_maritimo.creado_por')->where('id_poliza',$clause['id_poliza'])->where(function($query) use($clause,$sidx,$sord,$limit,$start){
            if($limit!=NULL) $query->skip($start)->take($limit);
        });
        if($sidx!=NULL && $sord!=NULL) $maritimo->orderBy($sidx, $sord);
        return $maritimo->get();
    }

    public function usuario(){
        $this->hasOne(Usuarios::class, 'id', 'creado_por');
    }


}