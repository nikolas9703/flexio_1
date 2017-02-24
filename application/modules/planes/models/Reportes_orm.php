<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Reportes_orm extends Model
{
    protected $table = 'seg_reportes';
    protected $fillable = ['uuid_reportes','nombre','id_aseguradora', 'id_ramo', 'updated_at', 'created_at'];
    protected $guarded = ['id'];
    public $timestamps = false;

    public function __construct(array $attributes = array())
    {
        $this->setRawAttributes(array_merge($this->attributes, array(
          'uuid_reportes' => Capsule::raw("ORDER_UUID(uuid())")
        )), true);
        parent::__construct($attributes);
    }
    public static function findByUuid($uuid){
        return self::where('uuid_reportes',hex2bin($uuid))->first();
    }

    public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){
        $reportes = self::where(function($query) use($clause,$sidx,$sord,$limit,$start){

            $query->where('id_aseguradora','=',$clause['id_aseguradora']);
            //if(isset($clause['nombre']))$query->where('nombre','like' ,"%".$clause['nombre']."%");
            //if(isset($clause['ruc']))$query->where('ruc','like' ,"%".$clause['ruc']."%");
            //if(isset($clause['telefono']))$query->where('telefono','like' ,"%".$clause['telefono']."%");
            //if(isset($clause['email']))$query->where('email','like',"%".$clause['email']."%");
            if($sidx!=NULL && $sord!=NULL)$query->orderBy($sidx, $sord);
            if($limit!=NULL) $query->skip($start)->take($limit);
        });
        //echo $reportes->getQuery()->toSql();
        return $reportes->get();
    }

    /*public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){
        $reportes = self::select(
            'seg_reportes.id',
            'seg_reportes.uuid_reportes',
            'seg_reportes.nombre as plan',
            'seg_ramos.nombre as area'
            )
            ->leftjoin("seg_ramos", function($join){
                $join->on("seg_ramos.id", "=", "seg_reportes.id_ramo");
            })
            ->where("seg_reportes.id_aseguradora", "=", $clause['id_aseguradora']);

            if($sidx!=NULL && $sord!=NULL)$reportes->orderBy('seg_reportes.nombre', $sord);
            if($limit!=NULL) $reportes->skip($start)->take($limit);

        //echo $reportes->getQuery()->toSql();
        return $reportes->get();
    }*/
}