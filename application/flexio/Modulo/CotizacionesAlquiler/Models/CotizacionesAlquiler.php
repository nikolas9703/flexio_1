<?php
namespace Flexio\Modulo\CotizacionesAlquiler\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Library\Util\GenerarCodigo;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;
use Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquiler;


class CotizacionesAlquiler extends Model
{

    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = false;
    protected $keepRevisionOf = ['codigo', 'cliente_id', 'empresa_id', 'fecha_hasta', 'fecha_desde', 'estado', 'creado_por', 'comentario', 'termino_pago', 'fecha_termino_pago', 'item_precio_id', 'subtotal', 'impuestos', 'total', 'descuento','centro_facturacion_id','tipo','cliente_tipo','centro_contable_id','lista_precio_alquiler_id'];

    protected $table = 'cotz_cotizaciones';
    protected $fillable = ['codigo', 'cliente_id', 'empresa_id', 'fecha_hasta', 'fecha_desde', 'estado', 'creado_por', 'comentario', 'termino_pago', 'fecha_termino_pago', 'item_precio_id', 'subtotal', 'impuestos', 'total', 'descuento','centro_facturacion_id','tipo','cliente_tipo','centro_contable_id','lista_precio_alquiler_id'];
    protected $guarded = ['id', 'uuid_cotizacion'];
    protected $appends= ['model_class','icono','enlace'];


    public function __construct(array $attributes = array())
    {
        $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_cotizacion' => Capsule::raw("ORDER_UUID(uuid())")
        )), true);
        parent::__construct($attributes);
    }

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public static function boot() {
        parent::boot();
    }

    public function getUuidCotizacionAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function getNumeroDocumentoAttribute()
    {
        return $this->codigo;
    }

    public function getModelClassAttribute(){
        return get_class($this);
    }

    public function getNumeroDocumentoEnlaceAttribute()
    {
        $attrs = [
            'href'  => $this->enlace,
            'class' => 'link'
        ];

        $html = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());
        return $html->setType('htmlA')->setAttrs($attrs)->setHtml($this->numero_documento)->getSalida();
    }

    public function getEnlaceAttribute()
    {
        return base_url('cotizaciones_alquiler/editar/'.$this->uuid_cotizacion);
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

    public function setFechaHastaAttribute($date)
    {
        return $this->attributes['fecha_hasta'] = Carbon::createFromFormat('d/m/Y', $date, 'America/Panama');
    }

    public function setCodigoAttribute($codigo){
        return $this->attributes['codigo'] = GenerarCodigo::setCodigo('QTA'.Carbon::now()->format('y'), $codigo);
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

    public function etapa_catalogo() {
        return $this->belongsTo('Flexio\Modulo\Cotizaciones\Models\CotizacionCatalogo', 'estado', 'etiqueta')->where('tipo', '=', 'etapa');
    }

    public function termino_pago_catalogo() {
        return $this->belongsTo('Flexio\Modulo\Cotizaciones\Models\CotizacionCatalogo', 'termino_pago', 'etiqueta')->where('tipo', '=', 'termino_pago');
    }

    public function cliente()
    {
        if($this->cliente_tipo == 'clientes_potenciales')
        {
            return $this->belongsTo('Flexio\Modulo\ClientesPotenciales\Models\ClientesPotenciales','cliente_id');
        }
        return $this->belongsTo('Flexio\Modulo\Cliente\Models\Cliente','cliente_id');
    }

    public function vendedor() {
        return $this->belongsTo('Flexio\Modulo\Usuarios\Models\Usuarios', 'creado_por');
    }

    public function cotizables()
    {
        return $this->hasMany('Flexio\Modulo\CotizacionesAlquiler\Models\CotizablesAlquiler', 'cotizacion_id');
    }


    public function items() {
         return $this->morphMany(CotizacionesAlquilerItems::class, 'contratable');
    }

    public function items_alquiler() {
         return $this->items()->where("item_adicional", 0);
    }

    public function items_adicionales() {
         return $this->items()->where("item_adicional", 1);
    }

    public function articulos()
    {
        return $this->morphMany('Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquilerItems', 'contratable');
    }

    public function centro_facturacion()
    {
        return $this->belongsTo('Flexio\Modulo\CentroFacturable\Models\CentroFacturable', 'centro_facturacion_id');
    }

    public function empresa()
    {
        return $this->belongsTo('Flexio\Modulo\Empresa\Models\Empresa','empresa_id');
    }

    public function scopeDeCodigo($query, $codigo = '')
    {
        return $query->where('codigo', 'like', '%'.$codigo.'%');
    }


    ///functiones del landing page
    public function getIconoAttribute(){
     return 'fa fa-line-chart';
    }
    public function landing_comments(){
        return $this->morphMany(Comentario::class,'comentable');
    }

    public function getCreadoPor() {
        $nombre = $this->hasMany('Flexio\Modulo\Usuarios\Models\Usuarios', 'id', 'creado_por')->first();

        return $nombre->nombre. ' ' .$nombre->apellido;
    }

    public function getCentroContable(){
        $centro_contable = $this->hasMany('Flexio\Modulo\CentrosContables\Models\CentrosContables', 'id', 'centro_contable_id')->first();
        return $centro_contable->nombre;
    }

    public function getCentroFacturacion(){
        $centrofacturacion = $this->hasMany('Flexio\Modulo\CentroFacturable\Models\CentroFacturable', 'id', 'centro_facturacion_id')->first();
        return $centrofacturacion;
    }

    public function present(){
         return new \Flexio\Modulo\CotizacionesAlquiler\Presenter\CotizacionesAlquilerPresenter($this);
    }

    public function contratos_alquiler_exist($id){
        //
        return Capsule::table('conalq_contratos_alquiler')
                ->select()
                ->where('tipoid','=',$id)
                ->get();
    }

    public function contratos_de_alquiler() {
        return $this->hasMany(ContratosAlquiler::class, 'tipoid');
    }

}
