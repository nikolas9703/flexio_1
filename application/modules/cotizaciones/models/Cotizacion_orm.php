<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Cotizacion_orm extends Model
{
	protected $table = 'cotz_cotizaciones';
	protected $fillable = ['codigo','cliente_id','empresa_id','fecha_hasta','fecha_desde','etapa','creado_por','comentario','termino_pago','fecha_termino_pago','item_precio_id','subtotal','impuestos','total','descuento'];
	protected $guarded = ['id','uuid_cotizacion'];

  public function __construct(array $attributes = array()){
    $this->setRawAttributes(array_merge($this->attributes, array(
      'uuid_cotizacion' => Capsule::raw("ORDER_UUID(uuid())")
    )), true);
    parent::__construct($attributes);
  }

	public static function boot(){

		parent::boot();
		static::updating(function($cotizacion) {
		//	dd($cotizacion);

		});
	}

	public function toArray(){
			$array = parent::toArray();
			return $array;
	}

  public function getCreatedAtAttribute($date){
    return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d H:i:s');
  }

	public function getFechaDesdeAttribute($date){
    return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('m/d/Y');
  }

	public function getFechaHastaAttribute($date){
    return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('m/d/Y');
  }

  public function getUuidCotizacionAttribute($value){
    return strtoupper(bin2hex($value));
  }

	public function items_cotizacion(){
		return $this->hasMany('Cotizacion_item_orm','cotizacion_id');
	}

	public function ordenes_validas(){
		return $this->hasMany('Orden_ventas_orm','cotizacion_id')->where(function($q){
			$q->where('estado','<>','anulada');
			$q->orWhere('estado','<>','perdida');
		});
	}

	public function empresa(){
		return $this->belongsTo('usuarios/Empresa_orm','empresa_id');
	}

	public function cliente(){
		return $this->belongsTo('Cliente_orm','cliente_id');
	}

	public function vendedor(){
		return $this->belongsTo('Usuario_orm','creado_por');
	}

	public function etapa_catalogo(){
		return $this->belongsTo('Cotizacion_catalogo_orm','etapa','etiqueta')->where('tipo','=','etapa');
	}

  static function lista_totales($clause=array()){
    return self::where(function($query) use($clause){
      $query->where('empresa_id','=',$clause['empresa_id']);
      if(isset($clause['cliente_id']))$query->where('cliente_id','=' ,$clause['cliente_id']);
      if(isset($clause['id']))$query->where('id','=' ,$clause['id']);
      if(isset($clause['etapa']))$query->where('etapa','=' ,$clause['etapa']);
      if(isset($clause['creado_por']))$query->where('creado_por','=',$clause['creado_por']);
			if(isset($clause['fecha_desde']))$query->where('fecha_desde','<=',$clause['fecha_desde']);
			if(isset($clause['fecha_hasta']))$query->where('fecha_hasta','>=',$clause['fecha_hasta']);
    })->count();
  }

  /**
  * function de listar y busqueda
  */
  public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){

      $cotizacion = self::where(function($query) use($clause){
      		$query->where('empresa_id','=',$clause['empresa_id']);
					if(isset($clause['cliente_id']))$query->where('cliente_id','=' ,$clause['cliente_id']);
					if(isset($clause['id']))$query->where('id','=' ,$clause['id']);
		      if(isset($clause['etapa']))$query->where('etapa','=' ,$clause['etapa']);
		      if(isset($clause['creado_por']))$query->where('creado_por','=',$clause['creado_por']);
					if(isset($clause['fecha_desde']))$query->where('fecha_desde','<=',$clause['fecha_desde']);
					if(isset($clause['fecha_hasta']))$query->where('fecha_hasta','>=',$clause['fecha_hasta']);
    	});
			if($sidx!==NULL && $sord!==NULL) $cotizacion->orderBy($sidx, $sord);
			if($limit!=NULL) $cotizacion->skip($start)->take($limit);

//dd($clientes->toSql());
//Capsule::enableQueryLog();
 //$cotizacion->get();
 //print_r(Capsule::getQueryLog());
  return $cotizacion->get();
  }
}
