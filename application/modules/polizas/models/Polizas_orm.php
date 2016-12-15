<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Polizas_orm extends Model
{
    protected $table = 'pol_polizas';
    protected $fillable = ['uuid_polizas','numero','cliente', 'ramo','usuario', 'estado', 'inicio_vigencia', 'fin_vigencia', 'creado_por', 'created_at', 'updated_at'];
    protected $guarded = ['id'];
    public $timestamps = false;


    public function __construct(array $attributes = array()){
        $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_aseguradora' => Capsule::raw("ORDER_UUID(uuid())")
        )), true);
        parent::__construct($attributes);
    }

    /**
     * Conteo de las aseguradoras existentes
     *
     * @return [array] [description]
     */
    public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){
        $aseguradoras = self::where(function($query) use($clause,$sidx,$sord,$limit,$start){
            //$query->where('empresa_id','=',$clause['empresa_id']);

            if(isset($clause['nombre']))$query->where('nombre','like' ,"%".$clause['nombre']."%");
            if(isset($clause['ruc']))$query->where('ruc','like' ,"%".$clause['ruc']."%");
            if(isset($clause['telefono']))$query->where('telefono','like' ,"%".$clause['telefono']."%");
            if(isset($clause['email']))$query->where('email','like',"%".$clause['email']."%");
            if($sidx!=NULL && $sord!=NULL)$query->orderBy($sidx, $sord);
            if($limit!=NULL) $query->skip($start)->take($limit);
        });

        return $aseguradoras->get();
    }



    public static function findByUuid($uuid){
        return self::where('uuid_aseguradora',hex2bin($uuid))->first();
    }
}
