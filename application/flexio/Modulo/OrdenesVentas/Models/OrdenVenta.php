<?php
namespace Flexio\Modulo\OrdenesVentas\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\Cotizaciones\Models\Cotizacion as Cotizacion;
use Flexio\Modulo\FacturasVentas\Models\FacturaVenta as FacturaVenta;
use Flexio\Modulo\Cotizaciones\Models\LineItem as LineItem;
use Flexio\Modulo\Cliente\Models\Cliente;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\Documentos\Models\Documentos;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;

class OrdenVenta extends Model
{

    use RevisionableTrait, RelacionOrdenTrabajo;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = ['codigo','cliente_id','empresa_id','fecha_hasta','fecha_desde','estado','created_by','comentario','termino_pago','fecha_termino_pago','item_precio_id','subtotal','impuestos','total','bodega_id','centro_contable_id','cotizacion_id','referencia','descuento','centro_facturacion_id' ,'formulario'];

    protected $table = 'ord_ventas';

    protected $fillable = ['codigo','cliente_id','empresa_id','fecha_hasta','fecha_desde','estado','created_by','comentario','termino_pago','fecha_termino_pago','item_precio_id','subtotal','impuestos','total','bodega_id','centro_contable_id','cotizacion_id','referencia','descuento','centro_facturacion_id' ,'formulario'];

    protected $guarded = ['id','uuid_venta'];
    protected $appends =['icono','enlace'];

  public function __construct(array $attributes = array()){
    $this->setRawAttributes(array_merge($this->attributes, array('uuid_venta' => Capsule::raw("ORDER_UUID(uuid())"))), true);
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

  public function getUuidVentaAttribute($value)
  {
      return strtoupper(bin2hex($value));
  }
    public function getUuidAttribute()
    {
        return $this->uuid_venta;
    }

  public function getCreatedAtAttribute($value)
  {
      return date("d-m-Y", strtotime($value));
  }

    public function getNumeroDocumentoAttribute()
    {
        return $this->codigo;
    }

    public function getModuloAttribute()
    {
        return 'Orden de venta';
    }

    public function getNumeroDocumentoEnlaceAttribute()
    {
        $attrs = [
            "href"  => $this->enlace,
            "class" => "link"
        ];

        $html = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory);
        return $html->setType("HtmlA")->setAttrs($attrs)->setHtml($this->numero_documento)->getSalida();
    }

    public function getClienteNombreAttribute(){
      if (is_null($this->cliente)) {
          return '';
      }
      return $this->cliente->nombre;
    }

    public function getVendedorNombreAttribute() {
        if (is_null($this->vendedor)) {
            return '';
        }
        return $this->vendedor->nombre . " " . $this->vendedor->apellido;
    }

    public function getEnlaceAttribute()
    {
        return base_url("ordenes_ventas/ver/".$this->uuid_venta);
    }
    public function getTimelineAttribute()
    {
        return [
            "Cliente: ".$this->cliente->nombre,
            "Bodega: ".$this->bodega->nombre_codigo,
            "Fecha: ".$this->created_at,
        ];
    }
    public function getTipoSpanAttribute()
    {
        $attrs  = [
            "style" => "float:right;color:#0070BA;"
        ];
        $html   = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());

        return $html->setType("htmlSpan")->setAttrs($attrs)->setHtml("Orden de venta")->getSalida();
    }
    public function getTipoFaAttribute()
    {
        $attrs = [
            "class" => "fa fa-line-chart",
        ];
        $html   = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());
        return  $html->setType("htmlI")->setAttrs($attrs)->setHtml('')->getSalida();
    }
    public function getTimeAgoAttribute()
    {
        return Carbon::createFromFormat("d-m-Y", $this->created_at)->diffForHumans();
    }
    public function getDiaMesAttribute()
    {
        return Carbon::createFromFormat("d-m-Y", $this->created_at)->formatLocalized('%d de %B');
    }
    public function getUbicacionAttribute()
    {
        return $this->cliente;
    }

    public function getExternoAttribute()
    {
        return $this->cliente;
    }

    public function getFechaHoraAttribute()
    {
      return Carbon::createFromFormat("d-m-Y", $this->updated_at)->format('d/m/Y @ H:i');
    }

    public function lines_items()
    {
        return $this->morphMany('Flexio\Modulo\Inventarios\Models\LinesItems', 'tipoable');
    }

  public function getUpdatedAtAttribute($value)
  {
      return date("d-m-Y", strtotime($value));
  }

    public function getFechaDesdeAttribute($date)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d/m/Y');
    }

    public function getFechaHastaAttribute($date)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d/m/Y');
    }

    public function setFechaDesdeAttribute($date)
    {
        return  $this->attributes['fecha_desde'] = Carbon::createFromFormat('d/m/Y', $date, 'America/Panama');
    }

    public function setFechaHastaAttribute($date)
    {
        return $this->attributes['fecha_hasta'] = Carbon::createFromFormat('d/m/Y', $date, 'America/Panama');
    }

  function getOrdenEstadoAttribute(){
      if(is_null($this->etapa_catalogo)){
          return '';
      }
      return $this->etapa_catalogo->valor;
  }




    public function bodega()
    {
        return $this->belongsTo('Flexio\Modulo\Bodegas\Models\Bodegas', 'bodega_id', 'id');
    }

    public function origen()
    {
        return $this->belongsTo('Flexio\Modulo\Bodegas\Models\Bodegas', 'bodega_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function destino()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function anticipos()
    {
        return $this->morphToMany('Flexio\Modulo\Anticipos\Models\Anticipo', 'empezable')->where('estado','aprobado');
    }

    public function anticipos_no_anulados()
    {
        return $this->morphToMany('Flexio\Modulo\Anticipos\Models\Anticipo', 'empezable')->whereIn('estado',['por_aprobar','aprobado']);
    }

  public function comp_numeroDocumento()
  {
      return $this->codigo;
  }

    public function items()
    {
        return $this->morphMany(LineItem::class,'tipoable');
    }

    public function items2()
    {
        return $this->morphToMany('Flexio\Modulo\Inventarios\Models\Items', 'tipoable', 'lines_items', 'tipoable_id', 'item_id')
                ->withPivot('id', 'uuid_line_item', 'categoria_id', 'empresa_id', 'cantidad', 'unidad_id', 'precio_unidad', 'impuesto_id', 'descuento', 'cuenta_id', 'precio_total', 'impuesto_total', 'descuento_total', 'observacion', 'cantidad2');;
    }

  public function vendedor(){
    return $this->belongsTo('Flexio\Modulo\Usuarios\Models\Usuarios','created_by');
  }

  public function facturas(){
    return $this->morphToMany(FacturaVenta::class,'fac_facturable')->withPivot('empresa_id','items_facturados');
  }

  public function etapa_catalogo(){
    return $this->belongsTo(OrdenVentaCatalogo::class,'estado','etiqueta')->where('tipo','=','etapa');
  }

  public function facturar(){
    if($this->estado =='por_facturar' || $this->estado =='facturado_parcial'){
      return true;
    }
    return false;
  }

  function cotizacion(){
      return $this->belongsTo(Cotizacion::class,'cotizacion_id');
  }

  function scopeDeEmpresa($query, $clause){
    return $query->where('empresa_id','=',$clause['empresa_id']);
  }
  function scopeEstadoValido($query){
    return  $query->whereIn('estado',array('por_facturar','facturado_parcial'));
  }
  function scopeFacturadoCompleto($query){
    return  $query->whereIn('estado',array('por_facturar','facturado_completo','facturado_parcial'));
  }
	public function comentario_timeline(){
     	    	return $this->morphMany(Comentario::class,'comentable');
    }

    public function getIconoAttribute(){
      return 'fa fa-line-chart';
    }
    public function landing_comments(){
       return $this->morphMany(Comentario::class,'comentable');
     }
     function documentos() {
    	return $this->morphMany(Documentos::class, 'documentable');
    }

  public function present(){
    return new \Flexio\Modulo\OrdenesVentas\Presenter\OrdenesVentaPresenter($this);
  }

    public function scopeDeFiltro($query, $campo)
    {
        $queryFilter = new \Flexio\Modulo\OrdenesVentas\Services\OrdenVentaFilters;
        return $queryFilter->apply($query, $campo);
    }

}
