<?php
namespace Flexio\Modulo\FacturasSeguros\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\Cotizaciones\Models\LineItem as LineItem;
use Flexio\Modulo\OrdenesVentas\Models\OrdenVenta as OrdenVenta;
use Flexio\Modulo\Contratos\Models\Contrato as Contrato;
use Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquiler;
use Flexio\Modulo\Devoluciones\Models\Devolucion as Devolucion;
use Flexio\Modulo\NotaCredito\Models\NotaCredito as NotaCredito;
use Flexio\Modulo\Cobros\Models\Cobro as Cobro;
use Flexio\Modulo\Documentos\Models\Documentos;
use Flexio\Modulo\Cliente\Models\Cliente;
use Flexio\Modulo\Polizas\Models\Polizas;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\FacturasCompras\Models\FacturaCompra;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;
use Flexio\Modulo\CentroFacturable\Models\CentroFacturable;
use Flexio\Modulo\Cotizaciones\Models\Cotizacion;
use Flexio\Library\Util\GenerarCodigo;


class FacturaSeguro extends Model
{
    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = ['cotizacion_id', 'orden_venta_id', 'codigo','cliente_id','empresa_id','fecha_hasta','fecha_desde','estado','created_by','comentario','termino_pago','fecha_termino_pago','item_precio_id','subtotal','impuestos','total','bodega_id','centro_contable_id','referencia','formulario','centro_facturacion_id','cargos_adicionales', 'cuenta' ];

    protected $table = 'fac_facturas';

    protected $fillable = ['cotizacion_id', 'orden_venta_id','codigo','cliente_id','empresa_id','fecha_hasta','fecha_desde','estado','created_by','comentario','termino_pago','fecha_termino_pago','item_precio_id','subtotal','impuestos','total' ,'descuento','bodega_id','centro_contable_id','referencia','formulario','centro_facturacion_id','cargos_adicionales', 'cuenta','id_poliza','otros','remesa_saliente','remesa_entrante','saldo','porcentaje_impuesto'];

    protected $guarded = ['id','uuid_factura'];  
    protected $appends =['icono','enlace'];
    protected $empezables = ['Flexio\Modulo\OrdenesVentas\Models\OrdenVenta'=> 'orden_venta','Flexio\Modulo\Contratos\Models\Contrato' => 'contrato_venta','Flexio\Modulo\OrdenesAlquiler\Models\OrdenVentaAlquiler'=>'orden_alquiler'];

    public function __construct(array $attributes = array()) {
      $this->setRawAttributes(array_merge($this->attributes, array('uuid_factura' => Capsule::raw("ORDER_UUID(uuid())"))), true);
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

    //Mutators
    public function getUuidFacturaAttribute($value) {
        return strtoupper(bin2hex($value));
    }
    public function getUuidAttribute() {
        return $this->uuid_factura;
    }
    public function getNumeroDocumentoAttribute() {
        return $this->codigo;
    }

    public function setCodigoAttribute($value){
        $year = Carbon::now()->format('y');
        return $this->attributes['codigo'] = GenerarCodigo::setCodigo('INV'.$year, $value);
    }

    function documentos() {
        return $this->morphMany(Documentos::class, 'documentable');
    }

    public function getNumeroDocumentoEnlaceAttribute() {
        $attrs = [
            "href"  => $this->enlace,
            "class" => "link"
        ];

        $html = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory);
        return $html->setType("HtmlA")->setAttrs($attrs)->setHtml($this->numero_documento)->getSalida();
    }

    public function getEnlaceAttribute() {
        return base_url("facturas/ver/".$this->uuid_factura);
    }

    public function getTimelineAttribute() {
        return [
            "Cliente: ".$this->cliente->nombre,
            "Bodega: ".$this->bodega->nombre_codigo,
            "Fecha: ".$this->fecha_desde,
        ];
    }

    public function getTipoSpanAttribute() {
        $attrs  = [
            "style" => "float:right;color:orange;"
        ];
        $html   = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());

        return $html->setType("htmlSpan")->setAttrs($attrs)->setHtml("Factura de Venta")->getSalida();
    }

    public function getTipoFaAttribute() {
        $attrs = [
            "class" => "fa fa-line-chart",
        ];
        $html   = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());
        return  $html->setType("htmlI")->setAttrs($attrs)->setHtml('')->getSalida();
    }

    public function getTimeAgoAttribute() {
        return Carbon::createFromFormat("m/d/Y", $this->fecha_desde)->diffForHumans();
    }

    public function getDiaMesAttribute() {
        return Carbon::createFromFormat("m/d/Y", $this->fecha_desde)->formatLocalized('%d de %B');
    }

    public function getUbicacionAttribute() {
        return $this->bodega;
    }
    public function getFechaDesdeAttribute($date) {
      return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d/m/Y');
    }

    public function getFechaHastaAttribute($date) {
      return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d/m/Y');
    }

    public function getEstadoFacturaAttribute() {
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

    public function getEmpezableTypeAttribute($value){

        if (count($this->empezable) == 0 || is_null($this->empezable)) {
            return null;
        }
        $value = $this->empezable->first()->fac_facturable_type;
        return $this->empezables[$value];

    }

    public function getEmpezableIdAttribute(){
        if (count($this->empezable) == 0) {
            return '';
        }
        return $this->empezable->first()->fac_facturable_id;
    }

    public function setFechaDesdeAttribute($date) {
        return  $this->attributes['fecha_desde'] = Carbon::createFromFormat('d/m/Y', $date, 'America/Panama');
    }

    public function setFechaHastaAttribute($date) {
      return $this->attributes['fecha_hasta'] = Carbon::createFromFormat('d/m/Y', $date, 'America/Panama');
    }


    public function setFechaHastaPdfAttribute() {
        return Carbon::createFromFormat("d/m/Y", $this->fecha_desde)->formatLocalized('%d-%m-%Y');
    }
    //Relationships
    public function bodega() {
        return $this->belongsTo('Bodegas_orm', 'bodega_id', 'id');
    }

    public function cliente() {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function comp_numeroDocumento() {
        return $this->codigo;
    }

    public function termino_pago2() {
        return $this->belongsTo(FacturaSeguroCatalogo::class, "termino_pago", "etiqueta");
    }

    public function items() {
        return $this->morphMany(LineItem::class,'tipoable');
    }
    public function items_venta() {
        return $this->items()->where("item_adicional", 0);
    }
    public function items_alquiler() {
          return $this->items()->where("item_adicional", 1);
    }
    public function items2() {
        return $this->morphToMany('Flexio\Modulo\Inventarios\Models\Items', 'tipoable', 'lines_items', 'tipoable_id', 'item_id')
                ->withPivot("item_id", "unidad_id", "precio_unidad", "cantidad", "precio_total");
    }

    public function vendedor() {
        return $this->belongsTo('Usuario_orm','created_by');
    }

    public function etapa_catalogo() {
        return $this->belongsTo(FacturaSeguroCatalogo::class,'estado','etiqueta')->where('tipo','=','etapa');
    }

    public function empezable(){
        return $this->hasMany(Facturable::class,'factura_venta_id');
    }

    public function ordenes_ventas() {
      return $this->morphedByMany(OrdenVenta::class,'fac_facturable')->withPivot('empresa_id','items_facturados');
    }

    public function orden_venta() {
        return $this->morphedByMany(OrdenVenta::class,'fac_facturable', null, 'factura_venta_id')->withPivot('empresa_id','items_facturados');
    }

    public function orden_alquiler() {
        return $this->morphedByMany('Flexio\Modulo\OrdenesAlquiler\Models\OrdenVentaAlquiler','fac_facturable')->withPivot('empresa_id','items_facturados');
    }

    public function contratos() {
      return $this->morphedByMany(Contrato::class,'fac_facturable')->withPivot('empresa_id');
    }

    public function contrato_venta() {
        return $this->morphedByMany(Contrato::class,'fac_facturable')->withPivot('empresa_id');
    }

    public function contratos_alquiler() {
        return $this->morphedByMany(ContratosAlquiler::class,'fac_facturable')
                ->withPivot('empresa_id');
    }

    public function contrato_alquiler() {
        return $this->morphedByMany(ContratosAlquiler::class,'fac_facturable')->withPivot('empresa_id');
    }

    public function refactura() {
      return $this->morphedByMany(FacturaCompra::Class,'fac_facturable')->withPivot('empresa_id');
      //return $this->morphedByMany('Facturas_compras_orm','fac_facturable')->withPivot('empresa_id');
    }

    function empresa() {
       return $this->belongsTo('Empresa_orm','empresa_id');
    }
    public function centro() {
        return $this->belongsTo('Centros_orm', 'centro_contable_id' );
    }

    public function centros_fac() {
        return $this->belongsTo(CentroFacturable::class, 'centro_facturacion_id');
    }
    public function scopeDeItem($query, $item_id) {
        return $query->whereHas("items", function($q) use ($item_id){
            $q->where("lines_items.item_id", $item_id);
        });
    }

    public function polizas(){
        return $this->belongsTo(Polizas::class, 'id_poliza');
    }

    public function scopeDeContratoAlquiler($query, $contrato_alquiler_id) {
        return $query->whereHas('contratos_alquiler',function($contrato_alquiler) use ($contrato_alquiler_id){
            $contrato_alquiler->where('conalq_contratos_alquiler.id', $contrato_alquiler_id);
        });
    }

    public function scopeDeContrato($query, $contrato_id) {
        return $query->whereHas('contratos',function($contrato) use ($contrato_id){
            $contrato->where('cont_contratos.id', $contrato_id);
        });
    }

    public function scopePorCobrar($query) {
      return $this->whereIn('estado',['por_cobrar','cobrado_parcial']);
    }

    public function scopeCobradoParcial($query) {
      return $this->where('estado','=','cobrado_parcial');
    }

    public function scopeCobradoCompleto($query) {
      return $this->where('estado','=','cobrado_completo');
    }
    public function scopeCobradoParcialCompleto($query) {
        return $this->whereIn('estado',['cobrado_completo','cobrado_parcial']);
    }
    public function scopeEstadosValidos($query) {
              return $this->whereIn('estado',['por_cobrar','cobrado_completo','cobrado_parcial']);

    }
//////////////////////////////////////////////////////////////
    function cobros() {
      return $this->belongsToMany(Cobro::class,'cob_cobro_facturas','cobrable_id','cobro_id')->where('cobrable_type','Flexio\Modulo\FacturasSeguros\Models\FacturaSeguro')
      ->withPivot('monto_pagado','empresa_id')->withTimestamps();
    }

    public function total_facturado() {
      return $this->cobros()->where('transaccion',1)->sum('cob_cobro_facturas.monto_pagado');
    }

    function devolucion() {
      return $this->hasOne(Devolucion::class,'factura_id');
    }

    public function nota_credito()
    {
        return $this->hasOne(NotaCredito::class,'factura_id');
    }

    public function nota_credito_aprobada()
    {
        return $this->hasOne(NotaCredito::class,'factura_id')->where('venta_nota_creditos.estado', 'aprobado');
    }

    function relacion_cobros() {
        return new Cobros($this);
    }

    function factura_cobros_aplicados() {
        return $this->cobros()->where('cob_cobros.estado','aplicado');
    }

    public static function getFacturas($clause = array(), $vista=null) {
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
    public function comentario_timeline() {
        return $this->morphMany(Comentario::class,'comentable');
    }

    public function getIconoAttribute() {
      return 'fa fa-line-chart';
    }
    public function landing_comments() {
       return $this->morphMany(Comentario::class,'comentable');
     }

    public function present() {
       return new  \Flexio\Modulo\FacturasSeguros\Presenter\FacturaSeguroPresenter($this);
    }

    public function cotizacion(){
        return $this->ordenes_ventas()->cotizacion;
    }

       public function scopeDeFiltro($query, $campo)
       {

           $queryFilter = new \Flexio\Modulo\FacturasSeguros\Services\FacturaSeguroFilters;
           return $queryFilter->apply($query, $campo);
       }

    
    
}
