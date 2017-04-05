<?php
namespace Flexio\Modulo\Agentes\Models;

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
//use Flexio\Modulo\Empresa\Models\Empresa;
//use Flexio\Politicas\PoliticableTrait;
use Flexio\Library\Util\FormRequest;
use Flexio\Library\Util\FlexioSession;
use Flexio\Modulo\Agentes\Models\AgentesRamos;

class Agentes extends Model
{

    //use PoliticableTrait;

    protected $table = 'agt_agentes';
    protected $fillable = ['uuid_agente','nombre', 'apellido', 'letra','identificacion', 'telefono', 'correo', 'porcentaje_participacion', 'estado', 'id_empresa', 'tipo_identificacion'];
    protected $guarded = ['id'];

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
            if((isset($clause['apellido'])) && (!empty($clause['apellido']))) $query->orwhere('apellido','like' ,"%".$clause['apellido']."%");
            if((isset($clause['telefono'])) && (!empty($clause['telefono']))) $query->where('telefono','like' ,"%".$clause['telefono']."%");
            if((isset($clause['identificacion'])) && (!empty($clause['identificacion']))) $query->where('identificacion','like' ,"%".$clause['identificacion']."%");
            if((isset($clause['correo'])) && (!empty($clause['correo']))) $query->where('correo','like',"%".$clause['correo']."%");
            if((isset($clause['id_empresa'])) && (!empty($clause['id_empresa']))) $query->where('id_empresa','=' , $clause['id_empresa']);
            if($limit!=NULL) $query->skip($start)->take($limit);            
        });
        if($sidx!=NULL && $sord!=NULL){
                $agentes->orderBy($sidx, $sord);
        }

        return $agentes->get();
    }

    public static function listar_agentes($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {

        $agtramos =  AgentesRamos::where("id_cliente", $clause['id_cliente'])->get();
        $ids = array();
        foreach ($agtramos as $val) {
            array_push($ids, $val->id_agente);
        }

        $agentes = self::where(function($query) use($clause,$sidx,$sord,$limit,$start, $ids){
            //$query->where('empresa_id','=',$clause['empresa_id']);           
            if((isset($clause['nombre'])) && (!empty($clause['nombre']))) $query->where('nombre','like' ,"%".$clause['nombre']."%");
            if((isset($clause['apellido'])) && (!empty($clause['apellido']))) $query->orwhere('apellido','like' ,"%".$clause['apellido']."%");
            if((isset($clause['telefono'])) && (!empty($clause['telefono']))) $query->where('telefono','like' ,"%".$clause['telefono']."%");
            if((isset($clause['identificacion'])) && (!empty($clause['identificacion']))) $query->where('identificacion','like' ,"%".$clause['identificacion']."%");
            if((isset($clause['correo'])) && (!empty($clause['correo']))) $query->where('correo','like',"%".$clause['correo']."%");
            if((isset($clause['id_empresa'])) && (!empty($clause['id_empresa']))) $query->where('id_empresa','=' , $clause['id_empresa']);
            if((isset($clause['id_cliente'])) && (!empty($clause['id_cliente']))) $query->whereIn('id',  $ids);
            if($limit!=NULL) $query->skip($start)->take($limit);            
        });
        if($sidx!=NULL && $sord!=NULL){
                $agentes->orderBy($sidx, $sord);
        }

        return $agentes->get();
    }

    public static function exportaragentes($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {
        $agentes = self::where(function($query) use($clause,$sidx,$sord,$limit,$start){
            //$query->where('empresa_id','=',$clause['empresa_id']);           
            if((isset($clause['ids'])) && (!empty($clause['ids']))) $query->whereIn('id', $clause['ids']);
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

    function present(){
        return new \Flexio\Modulo\Agentes\AgentesPresenter($this);
    }
}