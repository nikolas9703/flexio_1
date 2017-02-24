<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Cobro_orm extends Model
{

    protected $table = 'cob_cobros';

    protected $fillable = ['codigo','cliente_id','empresa_id','fecha_pago','estado','monto_pagado','cuenta_id','referencia'];

    protected $guarded = ['id','uuid_cobro'];

    public function __construct(array $attributes = array()){
      $this->setRawAttributes(array_merge($this->attributes, array('uuid_cobro' => Capsule::raw("ORDER_UUID(uuid())"))), true);
      parent::__construct($attributes);
    }

    public function getFechaPagoAttribute($date){
      return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('m/d/Y');
    }

    public function getUuidCobroAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function factura_cobros()
    {
      return $this->belongsToMany('Factura_orm','cob_cobro_facturas','cobro_id','factura_id')->withPivot('monto_pagado','empresa_id')->withTimestamps();
    }

    public function cobros_facturas(){
      return $this->hasMany('Cobro_factura_orm','cobro_id');
    }

    public function total_factura(){

    }
    public function total_cobrado(){
      return $this->hasMany('Cobro_factura_orm','cobro_id')->sum('monto_pagado');
    }

    public function metodo_pago()
    {
      return $this->hasMany('Cobro_metodo_pago_orm','cobro_id');
    }

    public function catalogo_estado()
    {
        return $this->belongsTo('Cobro_catalogo_orm','estado','etiqueta')->where('tipo','=','etapa');
    }


    public function cliente()
    {
        return $this->belongsTo('Cliente_orm', 'cliente_id');
    }

  public static function lista_totales($clause=array()){
      return self::where(function($query) use($clause){
        $query->where('empresa_id','=',$clause['empresa_id']);
        if(isset($clause['cliente_id']))$query->where('cliente_id','=' ,$clause['cliente_id']);
        if(isset($clause['etapa']))$query->where('estado','=' ,$clause['etapa']);
        if(isset($clause['codigo']))$query->where('codigo','like' ,"%".$clause['codigo']);
        if(isset($clause['creado_por']))$query->where('codigo','=',$clause['creado_por']);
        if(isset($clause['fecha_desde']))$query->where('fecha_pago','<=',$clause['fecha_desde']);
        if(isset($clause['fecha_hasta']))$query->where('fecha_pago','>=',$clause['fecha_hasta']);
      })->count();
    }

    /**
    * function de listar y busqueda
    */
    public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){
        $cobros = self::where(function($query) use($clause){
            $query->where('empresa_id','=',$clause['empresa_id']);
            if(isset($clause['cliente_id']))$query->where('cliente_id','=' ,$clause['cliente_id']);
            if(isset($clause['etapa']))$query->where('estado','=' ,$clause['etapa']);
            if(isset($clause['codigo']))$query->where('codigo','like' ,"%".$clause['codigo']);
            if(isset($clause['fecha_desde']))$query->where('fecha_pago','<=',$clause['fecha_desde']);
            if(isset($clause['fecha_hasta']))$query->where('fecha_pago','>=',$clause['fecha_hasta']);
        });
        if($sidx!=NULL && $sord!=NULL) $cobros->orderBy($sidx, $sord);
        if($limit!=NULL) $cobros->skip($start)->take($limit);
      return $cobros->get();
    }




  }
