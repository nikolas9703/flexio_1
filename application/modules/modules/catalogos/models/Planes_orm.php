<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Planes_orm extends Model
{
    protected $table = 'seg_planes';
    protected $fillable = ['uuid_planes','nombre','id_aseguradora', 'id_ramo', 'updated_at', 'created_at', 'desc_comision', 'id_impuesto'];
    protected $guarded = ['id'];
    public $timestamps = false;    

    public function __construct(array $attributes = array()) {
        $this->setRawAttributes(array_merge($this->attributes, array(
          'uuid_planes' => Capsule::raw("ORDER_UUID(uuid())")
        )), true);
        parent::__construct($attributes);
    }
    public static function findByUuid($uuid) {
        return self::where('uuid_planes',hex2bin($uuid))->first();
    }    
    public function ramos() {
		return $this->hasOne('Ramos_orm', 'id', 'id_ramo');
	}
    public function comisiones(){
	return $this->hasOne('Comisiones_orm', 'id_planes', 'id');
    }    
   /* public static function listar($clause = array(), $sidx = NULL, $sord = NULL, $limit = NULL, $start = NULL) {
        
        
        $planes = self::where(function($query) use($clause,$sidx,$sord,$limit,$start){
            $query->where('id_aseguradora','=',$clause['id_aseguradora']);
        });
        if($sidx!=NULL && $sord!=NULL){
            if($sidx != 'nombre_ramo'){
                $planes->orderBy($sidx, $sord);  
            }     
        }
        return $planes->get();
        
    }*/
    
    //transformaciones para GET
    public function getUuidPlanesAttribute($value) {
        return strtoupper(bin2hex($value));
    }    
    function getPlanes($clause) {
        $query = self::with(array('comisiones' => function($query){     
                }));
        $query->where('id_aseguradora', '=', $clause['id_aseguradora']);       
        return $query;                
    }
    public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){

        $planes = self::select(
            'seg_planes.id as id',
            'seg_planes.uuid_planes as uuid_planes',
            'seg_planes.nombre as plan',
            'producto.nombre as producto',
            'seg_ramos.nombre as ramo',
            'seg_planes.desc_comision as desc_comision',
            'comi.comision as comision',
            'comi.sobre_comision as sobre_comision'
            )
            ->leftjoin("seg_ramos", function($join){
                $join->on("seg_ramos.id", "=", "seg_planes.id_ramo");
            })->leftjoin("seg_ramos as producto", function($join){
                $join->on("producto.id", "=", "seg_ramos.padre_id");
            })->leftjoin("seg_planes_comisiones as comi", function($join){
                $join->on("comi.id_planes", "=", "seg_planes.id");
            })
            ->where("seg_planes.id_aseguradora", "=", $clause['id_aseguradora']);
            
            foreach($clause AS $field => $value)
            {  
                    //verificar si valor es array
                if(is_array($value)){                        
                    $planes->where($field, $value[0], $value[1]);                        
                }
            }


            if($sidx!=NULL && $sord!=NULL) $planes->orderBy($sidx, $sord);
            if($limit!=NULL) $planes->skip($start)->take($limit);


        
        return $planes->groupBy('seg_planes.id')->get();
    }
    public static function listarplanes($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){

        $planes = self::select(
            'seg_planes.id as id',
            'seg_planes.uuid_planes as uuid_planes',
            'seg_planes.nombre as plan',
            'producto.nombre as producto',
            'seg_ramos.nombre as ramo',
            'seg_planes.desc_comision as desc_comision',
            'comi.comision as comision',
            'comi.sobre_comision as sobre_comision',
			'aseg.nombre as nombre_aseguradora',
			'aseg.uuid_aseguradora as uuid_aseguradora'
            )
            ->leftjoin("seg_ramos", function($join){
                $join->on("seg_ramos.id", "=", "seg_planes.id_ramo");
            })->leftjoin("seg_ramos as producto", function($join){
                $join->on("producto.id", "=", "seg_ramos.padre_id");
            })->leftjoin("seg_planes_comisiones as comi", function($join){
                $join->on("comi.id_planes", "=", "seg_planes.id");
            })->leftjoin("seg_aseguradoras as aseg", function($join){
                $join->on("aseg.id", "=", "seg_planes.id_aseguradora");
            })
            ->whereIn("seg_planes.id", $clause['id']);

            if($sidx!=NULL && $sord!=NULL) $planes->orderBy($sidx, $sord);
            if($limit!=NULL) $planes->skip($start)->take($limit);
        
        
        return $planes->groupBy('seg_planes.id')->get();
    }
	
	public static function listartodo($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){

        $planes = self::select(
            'seg_planes.id as id',
            'seg_planes.uuid_planes as uuid_planes',
            'seg_planes.nombre as plan',
            'seg_ramos.nombre as producto',
            'seg_ramos.nombre as ramo',
            'seg_planes.desc_comision as desc_comision',
            'comi.comision as comision',
            'comi.sobre_comision as sobre_comision',
			'aseg.nombre as nombre_aseguradora',
			'aseg.uuid_aseguradora as uuid_aseguradora'
            )
            ->leftjoin("seg_ramos", function($join){
                $join->on("seg_ramos.id", "=", "seg_planes.id_ramo");
            })->leftjoin("seg_ramos as producto", function($join){
                $join->on("producto.id", "=", "seg_ramos.padre_id");
            })->leftjoin("seg_planes_comisiones as comi", function($join){
                $join->on("comi.id_planes", "=", "seg_planes.id");
            })->leftjoin("seg_aseguradoras as aseg", function($join){
                $join->on("aseg.id", "=", "seg_planes.id_aseguradora");
            });
            
            foreach($clause AS $field => $value)
            {  
                    //verificar si valor es array
                if(is_array($value)){                        
                    $planes->where($field, $value[0], $value[1]);                        
                }
				else
					$planes->where($field,$value);
            }

			if($sidx=="aseg.nombre1"){
				$planes->orderBy("aseg.nombre", "ASC")
				->orderBy("seg_planes.nombre", "ASC");
			}

            if($sidx!=NULL && $sord!=NULL && $sidx!="aseg.nombre1") $planes->orderBy($sidx, $sord);
            if($limit!=NULL) $planes->skip($start)->take($limit);

        return $planes->groupBy('seg_planes.id')->get();
    }

}