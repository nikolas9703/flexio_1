<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Orden_ventas_orm extends Model
{

    protected $table = 'ord_ventas';

    protected $fillable = ['codigo','cliente_id','empresa_id','fecha_hasta','fecha_desde','estado','created_by','comentario','termino_pago','fecha_termino_pago','item_precio_id','subtotal','impuestos','total','bodega_id','centro_contable_id','cotizacion_id','referencia','descuento'];

    protected $guarded = ['id','uuid_venta'];

    public function __construct(array $attributes = array()){
      $this->setRawAttributes(array_merge($this->attributes, array('uuid_venta' => Capsule::raw("ORDER_UUID(uuid())"))), true);
      parent::__construct($attributes);
    }


    public function getUuidVentaAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function getCreatedAtAttribute($value)
    {
        return date("d-m-Y", strtotime($value));
    }

    public function getUpdatedAtAttribute($value)
    {
        return date("d-m-Y", strtotime($value));
    }

    public function getFechaDesdeAttribute($date){
      return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('m/d/Y');
    }

  	public function getFechaHastaAttribute($date){
      return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('m/d/Y');
    }


    public function bodega()
    {
        return $this->belongsTo('Bodegas_orm', 'bodega_id', 'id');
    }

    public function cliente()
    {
        return $this->belongsTo('Cliente_orm', 'cliente_id');
    }

    public function comp_numeroDocumento()
    {
        return $this->codigo;
    }

    public function items_orden_ventas(){
  		return $this->hasMany('Ordenes_venta_item_orm','orden_venta_id');
  	}

    public function vendedor(){
  		return $this->belongsTo('Usuario_orm','created_by');
  	}

  	public function etapa_catalogo(){
  		return $this->belongsTo('Ordenes_catalogo_orm','estado','etiqueta')->where('tipo','=','etapa');
  	}

    public function facturar(){
      if($this->estado =='por_facturar' || $this->estado =='facturado_parcial'){
        return true;
      }else{
        return false;
      }
    }

    public function facturas(){
      	return $this->hasMany('Factura_orm','orden_venta_id');
    }

    function cotizacion(){
        return $this->belongsTo('Cotizacion_orm','cotizacion_id');
    }

    static function lista_totales($clause=array()){
      return self::where(function($query) use($clause){
        $query->where('empresa_id','=',$clause['empresa_id']);
        if(isset($clause['cotizacion_id']))$query->where('cotizacion_id','=' ,$clause['cotizacion_id']);
        if(isset($clause['id']))$query->where('id','=' ,$clause['id']);
        if(isset($clause['cliente_id']))$query->where('cliente_id','=' ,$clause['cliente_id']);
        if(isset($clause['etapa']))$query->where('estado','=' ,$clause['etapa']);
        if(isset($clause['creado_por']))$query->where('created_by','=',$clause['creado_por']);
  			if(isset($clause['fecha_desde']))$query->where('fecha_desde','<=',$clause['fecha_desde']);
  			if(isset($clause['fecha_hasta']))$query->where('fecha_hasta','>=',$clause['fecha_hasta']);
      })->count();
    }

    /**
    * @function de listar y busqueda
    */
    public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){
        $ordenes = self::where(function($query) use($clause){
        		$query->where('empresa_id','=',$clause['empresa_id']);
            if(isset($clause['cotizacion_id']))$query->where('cotizacion_id','=' ,$clause['cotizacion_id']);
            if(isset($clause['id']))$query->where('id','=' ,$clause['id']);
  					if(isset($clause['cliente_id']))$query->where('cliente_id','=' ,$clause['cliente_id']);
  		      if(isset($clause['etapa']))$query->where('estado','=' ,$clause['etapa']);
  		      if(isset($clause['creado_por']))$query->where('created_by','=',$clause['creado_por']);
  					if(isset($clause['fecha_desde']))$query->where('fecha_desde','<=',$clause['fecha_desde']);
  					if(isset($clause['fecha_hasta']))$query->where('fecha_hasta','>=',$clause['fecha_hasta']);
      	});
        if($sidx!=NULL && $sord!=NULL) $ordenes->orderBy($sidx, $sord);
        if($limit!=NULL) $ordenes->skip($start)->take($limit);
      return $ordenes->get();
    }

    function scopeDeEmpresa($query, $clause){
      return $query->where('empresa_id','=',$clause['empresa_id']);
    }
    function scopeEstadoValido($query){
      return  $query->whereIn('estado',array('abierta','por_facturar','facturado_parcial'));
    }

}
