<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Agentes_orm extends Model
{
    protected $table = 'agt_agentes';
    protected $fillable = ['nombre', 'apellido', 'letra','identificacion', 'telefono', 'correo', 'porcentaje_participacion'];
    protected $guarded = ['id', 'uuid_agente'];

    public function __construct(array $attributes = array()) {
        $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_agente' => Capsule::raw("ORDER_UUID(uuid())")
        )), true);
        parent::__construct($attributes);
    }

    /**
     * Conteo de las aseguradoras existentes
     *
     * @return [array] [description]
     */
    public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {
        $agentes = self::where(function($query) use($clause,$sidx,$sord,$limit,$start){
            //$query->where('empresa_id','=',$clause['empresa_id']);           
            if((isset($clause['nombre'])) && (!empty($clause['nombre']))) $query->where('nombre','like' ,"%".$clause['nombre']."%");
            if((isset($clause['apellido'])) && (!empty($clause['apellido']))) $query->where('apellido','like' ,"%".$clause['apellido']."%");
            if((isset($clause['telefono'])) && (!empty($clause['telefono']))) $query->where('telefono','like' ,"%".$clause['telefono']."%");
            if((isset($clause['correo'])) && (!empty($clause['correo']))) $query->where('correo','like',"%".$clause['correo']."%");
            if($limit!=NULL) $query->skip($start)->take($limit);            
        });
        if($sidx!=NULL && $sord!=NULL){
                $agentes->orderBy($sidx, $sord);
        }

        return $agentes->get();
    }

    public function  uuid() {
        return $this->uuid_agente;
    }

    public function getUuidAgenteAttribute($value) {
        return strtoupper(bin2hex($value));
    }

    public static function findByUuid($uuid) {
        return self::where('uuid_agente',hex2bin($uuid))->first();
    }
}