<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Beneficiarios_orm extends Model
{
    protected $table = 'ben_beneficiarios';
    protected $fillable = ['uuid_beneficiario','nombre','identificacion', 'tomo_rollo','folio_imagen_doc', 'asiento_ficha', 'digito_verificador', 'cargo', 'telefono', 'correo', 'id_sexo', 'fecha_nacimiento', 'edad', 'estatura', 'peso', 'direccion', 'estado', 'observaciones', 'creado_por', 'created_at', 'updated_at'];
    protected $guarded = ['id'];
    public $timestamps = false;


    public function __construct(array $attributes = array()){
        $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_beneficiario' => Capsule::raw("ORDER_UUID(uuid())")
        )), true);
        parent::__construct($attributes);
    }

    /**
     * Conteo de los beneficiarios existentes
     *
     * @return [array] [description]
     */
    public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){
        $beneficiarios = self::where(function($query) use($clause,$sidx,$sord,$limit,$start){
            //$query->where('empresa_id','=',$clause['empresa_id']);

            if(isset($clause['nombre']))$query->where('nombre','like' ,"%".$clause['nombre']."%");
            if(isset($clause['ruc']))$query->where('ruc','like' ,"%".$clause['ruc']."%");
            if(isset($clause['telefono']))$query->where('telefono','like' ,"%".$clause['telefono']."%");
            if(isset($clause['email']))$query->where('email','like',"%".$clause['email']."%");
            if($sidx!=NULL && $sord!=NULL)$query->orderBy($sidx, $sord);
            if($limit!=NULL) $query->skip($start)->take($limit);
        });

        return $beneficiarios->get();
    }



    public static function findByUuid($uuid){
        return self::where('uuid_beneficiario',hex2bin($uuid))->first();
    }
}