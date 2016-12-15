<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Presupuesto_orm extends Model
{
	protected $table = 'pres_presupuesto';
	protected $fillable = ['codigo','nombre','empresa_id','fecha_inicio','centro_contable_id','cantidad_meses'];
	protected $guarded = ['id','uuid_presupuesto'];

  public function __construct(array $attributes = array()){
    $this->setRawAttributes(array_merge($this->attributes, array(
      'uuid_presupuesto' => Capsule::raw("ORDER_UUID(uuid())")
    )), true);
    parent::__construct($attributes);
  }

	public function toArray(){
			$array = parent::toArray();
			$array['inicio'] = Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['fecha_inicio'])->format('m-Y');
			return $array;
	}

  public function getCreatedAtAttribute($date){
    return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d H:i:s');
  }

  public function getUuidPresupuestoAttribute($value){
    return strtoupper(bin2hex($value));
  }

  public function centro_contable(){
			return $this->belongsTo('Centros_orm','centro_contable_id');
	}

  public function lista_presupuesto(){
		return $this->hasMany('Centro_cuenta_presupuesto_orm','presupuesto_id');
	}
  public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){
  $presupuestos = self::where(function($query) use($clause,$sidx,$sord,$limit,$start){
		$query->where('empresa_id','=',$clause['empresa_id']);
		if(isset($clause['centro_contable_id']))$query->where('centro_contable_id','=' ,$clause['centro_contable_id']);
		if(isset($clause['nombre']))$query->where('nombre','like' ,"%".$clause['nombre']."%");
		if(isset($clause['fecha1']))$query->where('fecha_inicio','>',$clause['fecha1']);
		if(isset($clause['fecha2']))$query->where('fecha_inicio','<',$clause['fecha2']);
		if($sidx!=NULL && $sord!=NULL) $query->orderBy($sidx, $sord);
	  if($limit!=NULL) $query->skip($start)->take($limit);
	});

  return $presupuestos->get();
  }
}
