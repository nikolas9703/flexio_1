<?php
namespace Flexio\Modulo\Polizas\Models;

use Illuminate\Database\Eloquent\Model as Model;

class PolizasPersonas extends Model
{
	
	protected $table = 'pol_poliza_personas'; 
	protected $fillable =[
    "id", 
    "id_interes",
    "id_poliza",
    "numero",
    "nombrePersona" ,
    "identificacion",
    "fecha_nacimiento",
    "estado_civil",
    "nacionalidad",
    "sexo",
    "estatura",
    "peso",
    "telefono_residencial",
    "telefono_oficina",
    "direccion_residencial",
    "direccion_laboral",
    "observaciones","updated_at",
    "created_at",
    "empresa_id",
    "telefono_principal",
    "direccion_principal",
    "detalle_relacion",
    "detalle_int_asociado",
    "detalle_certificado",
    "detalle_beneficio",
    "detalle_monto",
    "detalle_prima",
    "estado",
    "fecha_inclusion",
    "detalle_participacion",
    "detalle_suma_asegurada",
    "detalle_unico",
    "correo",
    "tipo_relacion"]; 
    public $timestamps = false;

    public static function listar_personas_provicional($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL,$id_poliza) {
        $mainQuery['id_poliza'] =$id_poliza;
        if(isset($clause['desde'])){
            if($clause['desde']=="renovar"){
                $mainQuery=[];
                $mainQuery['detalle_unico'] =$clause['detalleUnico'];
            }
        }

        $personas = self::where($mainQuery)->where(function($query) use($clause,$sidx,$sord,$limit,$start){

            if((isset($clause['detalle_relacion'])) && (!empty($clause['detalle_relacion']))) $query->where('detalle_relacion','=' , $clause['detalle_relacion']);
            if((isset($clause['id_interes'])) && (!empty($clause['id_interes']))){
            
             $query->where('detalle_int_asociado','=' , $clause['id_interes']); 
            }
            if($limit!=NULL) $query->skip($start)->take($limit);            
        });

        if (isset($clause['nombrePersona']) && $clause['nombrePersona'] != "" ) {
            $personas->where("nombrePersona", "LIKE", "%".$clause['nombrePersona']."%");
        }
        if (isset($clause['identificacion']) && $clause['identificacion'] != "" ) {
            $personas->where("identificacion", "LIKE", "%".$clause['identificacion']."%");
        }
        if (isset($clause['no_certificado']) && $clause['no_certificado'] != "" ) {
            $personas->where("detalle_certificado", "LIKE", "%".$clause['no_certificado']."%");
        }
        
        if($sidx!=NULL && $sord!=NULL){ $personas->orderBy($sidx, $sord); }

        return $personas->get();
    }


}