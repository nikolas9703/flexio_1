<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Remesas_orm extends Model
{
    protected $table = 'seg_remesas';
    protected $fillable = ['id','uuid_remesa','remesa','fecha', 'archivo','poliza', 'usuario', 'creado_por', 'created_at', 'updated_at'];
    protected $guarded = ['id'];
    public $timestamps = false;


    public function __construct(array $attributes = array()){
        $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_remesa' => Capsule::raw("ORDER_UUID(uuid())")
        )), true);
        parent::__construct($attributes);
    }

    /**
     * Conteo de las remesas existentes
     *
     * @return [array] [description]
     */
    public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){
        $remesas = self::where(function($query) use($clause,$sidx,$sord,$limit,$start){
            //$query->where('empresa_id','=',$clause['empresa_id']);

            if(isset($clause['remesa']))$query->where('remesa','like' ,"%".$clause['remesa']."%");
            if(isset($clause['fecha']))$query->where('fecha','like' ,"%".$clause['fecha']."%");
            if(isset($clause['poliza']))$query->where('poliza','like' ,"%".$clause['poliza']."%");
            if($sidx!=NULL && $sord!=NULL)$query->orderBy($sidx, $sord);
            if($limit!=NULL) $query->skip($start)->take($limit);
        });

        return $remesas->get();
    }



    public static function findByUuid($uuid){
        return self::where('uuid_remesa',hex2bin($uuid))->first();
    }
}
