<?php

namespace Flexio\Modulo\Cotizaciones\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\Cotizaciones\Models\CotizacionCatalogo as CotizacionCatalogo;
use Flexio\Modulo\Cotizaciones\Models\LineItem as LineItem;
use Flexio\Modulo\OrdenesVentas\Models\OrdenVenta as OrdenVenta;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\CentroFacturable\Models\CentroFacturable;
use Flexio\Modulo\Cliente\Models\Cliente;
use Flexio\Modulo\Documentos\Models\Documentos;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;
use Flexio\Modulo\CentrosContables\Models\CentrosContables;

class Cotizacion extends Model {
    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf =['codigo', 'cliente_id', 'empresa_id', 'fecha_hasta', 'fecha_desde', 'estado', 'creado_por', 'comentario', 'termino_pago', 'fecha_termino_pago', 'item_precio_id', 'subtotal', 'impuestos', 'total', 'descuento','centro_facturacion_id','centro_contable_id','cliente_tipo'];

    protected $table = 'cotz_cotizaciones';
    protected $fillable = ['codigo', 'cliente_id', 'empresa_id', 'fecha_hasta', 'fecha_desde', 'estado', 'creado_por', 'comentario', 'termino_pago', 'fecha_termino_pago', 'item_precio_id', 'subtotal', 'impuestos', 'total', 'descuento','centro_facturacion_id','centro_contable_id','cliente_tipo'];
    protected $guarded = ['id', 'uuid_cotizacion'];
    protected $appends =['icono','enlace'];

    public function __construct(array $attributes = array()) {
        $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_cotizacion' => Capsule::raw("ORDER_UUID(uuid())")
                )), true);
        parent::__construct($attributes);
    }

    public function centro_facturacion(){

        return $this->belongsTo('Flexio\Modulo\CentroFacturable\Models\CentroFacturable', 'centro_facturacion_id');

    }

    public static function boot() {

        parent::boot();
        static::updating(function($cotizacion) {

            $cambio = $cotizacion->getDirty();
            $original = $cotizacion->getOriginal();
            //dd($cambio, $original);
            if(isset($cambio['estado'])){
                $catalogo_anterior = CotizacionCatalogo::where("etiqueta","=",$original['estado'])->get();
                $catalogo_actual = CotizacionCatalogo::where("etiqueta","=",$cambio['estado'])->get();
                //$cotizacion->load('estados');
                //dd($catalogo_anterior[0]->valor, $catalogo_actual[0]->valor);
                $descripcion = "<b style='color:#0080FF; font-size:15px;'>Cambio de estado en la cotización</b></br></br>";
                $descripcion .= "Estado actual: ".$catalogo_actual[0]->valor.'</br></br>';
                $descripcion .= "Estado anterior: ".$catalogo_anterior[0]->valor;

                $create = [
                    'codigo' => $cotizacion->codigo,
                    'usuario_id' => $cotizacion->creado_por,
                    'empresa_id' => $cotizacion->empresa_id,
                    'cotizacion_id'=> $cotizacion->id,
                    'tipo'   => "actualizado",
                    'descripcion' => $descripcion
                ];
                //dd($create);
                CotizacionHistorial::create($create);
                return $cotizacion;
            }

        });
        static::created(function($cotizacion){

            $create = [
                'codigo' => $cotizacion->codigo,
                'usuario_id' => $cotizacion->creado_por,
                'empresa_id' => $cotizacion->empresa_id,
                'cotizacion_id'=> $cotizacion->id,
                'tipo'   => "creado",
                'descripcion' => "<b style='color:#0080FF; font-size:15px;'>Se creó la cotización</b></br></br>No. ".$cotizacion->codigo."</br></br>Estado: Por aprobar"
            ];
            CotizacionHistorial::create($create);
            return $cotizacion;
        });
    }

    public function toArray() {
        $array = parent::toArray();
        return $array;
    }

    public function getCreatedAtAttribute($date) {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d H:i:s');
    }

    public function getImprimibleAttribute() {
        return $this->estado == 'aprobado';
    }


    public function oportunidades(){

        return $this->morphToMany('Flexio\Modulo\Oportunidades\Models\Oportunidades','relacionable','opo_oportunidades_relaciones','relacionable_id','oportunidad_id');

    }

    public function scopeDeOportunidad($query, $oportunidad_id){

        return $query->whereHas('oportunidades',function($oportunidad) use ($oportunidad_id){

            $oportunidad->where('opo_oportunidades.id',$oportunidad_id);

        });

    }

    public function getEstadoCotizacionAttribute() {
        if (is_null($this->etapa_catalogo)) {
            return '';
        }
        return $this->etapa_catalogo->valor;
    }

    public function getVendedorNombreAttribute() {
        if (is_null($this->vendedor)) {
            return '';
        }
        return $this->vendedor->nombre . " " . $this->vendedor->apellido;
    }

    public function getClienteNombreAttribute() {
        if (is_null($this->cliente)) {
            return '';
        }
        return $this->cliente->nombre;
    }

    public function setFechaHastaAttribute($date)
    {
        return $this->attributes['fecha_hasta'] = Carbon::createFromFormat('d/m/Y', $date, 'America/Panama');
    }

    public function getFechaHastaAttribute($date)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d/m/Y');
    }

    public function getFechaDesdeAttribute($date)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d/m/Y');
    }

    public function setFechaDesdeAttribute($date)
    {
        return $this->attributes['fecha_desde'] = Carbon::createFromFormat('d/m/Y', $date, 'America/Panama');
    }

    public function scopeDeNoCotizacion($query, $no_cotizacion)
    {
        return $query->where('codigo', 'like', '%'.$no_cotizacion.'%');
    }

    public function getUuidCotizacionAttribute($value) {
        return strtoupper(bin2hex($value));
    }

    public function items() {
        return $this->morphMany(LineItem::class, 'tipoable');
    }

    public function orden_venta() {
        return $this->hasOne(OrdenVenta::class, 'cotizacion_id');
    }

    public function ordenes_de_ventas(){
        return $this->hasMany(OrdenVenta::class, 'cotizacion_id');
    }

    public function scopeDeOrdenVenta($query, $orden_venta_id){

        return $query->whereHas('orden_venta',function($orden_venta) use ($orden_venta_id){

            $orden_venta->where('ord_ventas.id',$orden_venta_id);

        });

    }

    function ordenes_validas() {
        return $this->orden_venta()->where(function($q) {
                    $q->where('estado', '<>', 'anulada');
                    $q->orWhere('estado', '<>', 'perdida');
                });
    }

    public function empresa() {
        return $this->belongsTo('Empresa_orm', 'empresa_id');
    }

    public function cliente() {

        if($this->cliente_tipo == 'cliente'){

            return $this->belongsTo(Cliente::class, 'cliente_id');

        }

        return $this->belongsTo('Flexio\Modulo\ClientesPotenciales\Models\ClientesPotenciales', 'cliente_id');

    }

    public function vendedor() {
        return $this->belongsTo('Usuario_orm', 'creado_por');
    }

    public function etapa_catalogo() {
        return $this->belongsTo(CotizacionCatalogo::class, 'estado', 'etiqueta')->where('tipo', '=', 'etapa');
    }

    public function termino_pago_catalogo() {
        return $this->belongsTo(CotizacionCatalogo::class, 'termino_pago', 'etiqueta')->where('tipo', '=', 'termino_pago');
    }
	public function comentario_timeline(){
     	    	return $this->morphMany(Comentario::class,'comentable');
    }
    public function comentario_items(){
    	return $this->morphMany(Comentario::class,'comentable');
    }
    public function relacion_centro_facturables(){
        return $this->morphToMany(CentroFacturable::class,'serviceable','centros_facturables');
    }

    public function formatEstado(){
        return new EstadoPresenter($this);
    }

    ///functiones del landing page
    public function getIconoAttribute(){
     return 'fa fa-line-chart';
    }
    public function getEnlaceAttribute() {
        return base_url("cotizaciones/ver/".$this->uuid_cotizacion);
    }
    public function landing_comments(){
        return $this->morphMany(Comentario::class,'comentable');
    }
    function documentos() {
    	return $this->morphMany(Documentos::class, 'documentable');
    }

    public function scopeDeFiltro($query, $campo)
    {
        $queryFilter = new \Flexio\Modulo\Cotizaciones\Services\CotizacionFilters;
        return $queryFilter->apply($query, $campo);
    }

    public function centro_contable() {
        return $this->belongsTo(CentrosContables::class,'centro_contable_id');
    }

    public function estados()
    {
        return $this->belongsTo(CotizacionCatalogo::class, 'estado');
    }

    public function historial(){
        return $this->hasMany(CotizacionHistorial::class,'cotizacion_id');
    }
    public function getEnlaceBitacoraAttribute()
    {
        return base_url('cotizaciones/historial/'.$this->uuid_cotizacion);
    }

}
