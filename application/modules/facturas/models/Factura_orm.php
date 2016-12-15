<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Factura_orm extends Model
{
 
    protected $table = 'fac_facturas';

    protected $fillable = ['codigo','cliente_id','empresa_id','fecha_hasta','fecha_desde','estado','created_by','comentario','termino_pago','fecha_termino_pago','item_precio_id','subtotal','impuestos','total','bodega_id','centro_contable_id','cotizacion_id','referencia','orden_venta_id'];

    protected $guarded = ['id','uuid_factura'];

    public function __construct(array $attributes = array()){
      $this->setRawAttributes(array_merge($this->attributes, array('uuid_factura' => Capsule::raw("ORDER_UUID(uuid())"))), true);
      parent::__construct($attributes);
    }



    public function getUuidFacturaAttribute($value)
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

    public function items_factura(){
  		return $this->hasMany('Factura_items_orm','factura_id');
  	}

    public function vendedor(){
  		return $this->belongsTo('Usuario_orm','created_by');
  	}

  	public function etapa_catalogo(){
  		return $this->belongsTo('Factura_catalogo_orm','estado','etiqueta')->where('tipo','=','etapa');
  	}

    function orden_venta(){
        return $this->belongsTo('Orden_ventas_orm','orden_venta_id');
    }

    function cotizacion(){
        return $this->belongsTo('Cotizacion_orm','cotizacion_id');
    }

    function empresa(){
       return $this->belongsTo('Empresa_orm','empresa_id');
    }

    function cobros(){
      return $this->belongsToMany('Cobro_orm','cob_cobro_facturas','factura_id','cobro_id')->withPivot('monto_pagado','empresa_id')->withTimestamps();
    }

    public function total_facturado(){
      return $this->cobros()->sum('cob_cobro_facturas.monto_pagado');
    }

    public static function getFacturas($clause = array(), $vista=null)
    {
      if(!empty($clause)){
        $facturas = self::where(function($query) use($clause, $vista){
          $query->where('empresa_id','=',$clause['empresa_id']);
          if($vista == 'registrar_pago_cobro'){
            $query->whereIn('estado',array('por_aprobar'));
        }elseif($vista =='crear'){
            $query->where('estado','=','por_aprobar');
        }else{
              $query->whereNotIn('estado',array('anulada'));
          }
        })->get(array('uuid_factura','codigo','cliente_id'));
        return $facturas;
      }elseif(empty($clause)){
        return array();
      }
    }

    static function lista_totales($clause=array()){
      return self::where(function($query) use($clause){
        $query->where('empresa_id','=',$clause['empresa_id']);
        if(isset($clause['cotizacion_id']))$query->where('cotizacion_id','=' ,$clause['cotizacion_id']);
        if(isset($clause['cliente_id']))$query->where('cliente_id','=' ,$clause['cliente_id']);
        if(isset($clause['etapa']))$query->where('estado','=' ,$clause['etapa']);
        if(isset($clause['creado_por']))$query->where('created_by','=',$clause['creado_por']);
  			if(isset($clause['fecha_desde']))$query->where('fecha_desde','<=',$clause['fecha_desde']);
  			if(isset($clause['fecha_hasta']))$query->where('fecha_hasta','>=',$clause['fecha_hasta']);
      })->count();
    }

    /**
    * function de listar y busqueda
    */
    public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){
        $facturas = self::where(function($query) use($clause){
        		$query->where('empresa_id','=',$clause['empresa_id']);
            if(isset($clause['cotizacion_id']))$query->where('cotizacion_id','=' ,$clause['cotizacion_id']);
  					if(isset($clause['cliente_id']))$query->where('cliente_id','=' ,$clause['cliente_id']);
  		      if(isset($clause['etapa']))$query->where('estado','=' ,$clause['etapa']);
  		      if(isset($clause['creado_por']))$query->where('created_by','=',$clause['creado_por']);
  					if(isset($clause['fecha_desde']))$query->where('fecha_desde','<=',$clause['fecha_desde']);
  					if(isset($clause['fecha_hasta']))$query->where('fecha_hasta','>=',$clause['fecha_hasta']);
      	});
        if($sidx!=NULL && $sord!=NULL) $facturas->orderBy($sidx, $sord);
        if($limit!=NULL) $facturas->skip($start)->take($limit);
      return $facturas->get();
    }
    
    

}
