<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Aseguradoras_orm extends Model
{
    protected $table = 'seg_aseguradoras';
    protected $fillable = ['uuid_aseguradora','nombre','ruc', 'telefono','email', 'uuid_cuenta_pagar', 'uuid_cuenta_cobrar', 'direccion', 'descuenta_comision', 'imagen_archivo', 'creado_por', 'created_at', 'updated_at','empresa_id'];
    protected $guarded = ['id'];
    public $timestamps = false;


    public function __construct(array $attributes = array()) {
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
    public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {
        $aseguradoras = self::where(function($query) use($clause,$sidx,$sord,$limit,$start){
            //$query->where('empresa_id','=',$clause['empresa_id']);

            if(isset($clause['nombre']))$query->where('nombre','like' ,"%".$clause['nombre']."%");
            if(isset($clause['ruc']))$query->where('ruc','like' ,"%".$clause['ruc']."%");
            if(isset($clause['telefono']))$query->where('telefono','like' ,"%".$clause['telefono']."%");
            if(isset($clause['email']))$query->where('email','like',"%".$clause['email']."%");
            if(isset($clause['id']))$query->whereIn('id',$clause['id']);            
            if($limit!=NULL) $query->skip($start)->take($limit);
        });
        if($sidx!=NULL && $sord!=NULL){
        $aseguradoras->orderBy($sidx, $sord);     
        }
        return $aseguradoras->get();
    }

    //transformaciones para GET
    public function getUuidCuentaPagarAttribute($value) {
        return strtoupper(bin2hex($value));
    }
    //transformaciones para GET
    public function getUuidCuentaCobrarAttribute($value) {
        return strtoupper(bin2hex($value));
    }
    //transformaciones para GET
    public function getUuidAseguradoraAttribute($value) {
        return strtoupper(bin2hex($value));
    }

    public static function findByUuid($uuid) {
        return self::where('uuid_aseguradora',hex2bin($uuid))->first();
    }
    public static function aseguradoras_listar($clause) {
        $aseguradoras = self::with(array('coberturas' => function($query){            
        }));
        $aseguradoras->where($clause);
        return $aseguradoras;
    }
    public function coberturas(){
	return $this->hasMany('Coberturas_orm', 'id');
    }

    public static function findByIdAseguradora($id_aseguradora){
        return self::where('id','=',$id_aseguradora)->first();
    }
}