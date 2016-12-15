<?php
namespace Flexio\Modulo\ContratosAlquiler\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\Documentos\Models\Documentos;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;

class ContratosAlquiler extends Model
{

    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = ['codigo','cliente_id','fecha_inicio','fecha_fin','saldo_facturar','total_facturado','estado_id','empresa_id','created_by','created_at','updated_at','observaciones','referencia','centro_facturacion_id','centro_contable_id','corte_facturacion_id','facturar_contra_entrega_id','dia_corte', 'calculo_costo_retorno_id','lista_precio_alquiler_id'];

    protected $table    = 'conalq_contratos_alquiler';
    protected $fillable = ['codigo','cliente_id','fecha_inicio','fecha_fin','saldo_facturar','total_facturado','estado_id','empresa_id','created_by','created_at','updated_at','observaciones','referencia','centro_facturacion_id','facturar_contra_entrega_id','centro_contable_id','tipo', 'calculo_costo_retorno_id','lista_precio_alquiler_id'];
    protected $guarded  = ['id','uuid_contrato_alquiler'];
    protected $appends  = ['fecha_format','fecha_inicio_format','fecha_fin_format','icono','enlace','enlace_bitacora'];

    public function __construct(array $attributes = array())
    {
        $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_contrato_alquiler' => Capsule::raw("ORDER_UUID(uuid())")
        )), true);
        parent::__construct($attributes);
    }

    public function getUuidContratoAlquilerAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function getNumeroDocumentoAttribute()
    {
        return $this->codigo;
    }

    public function ordenes_alquiler()
    {
        return $this->hasMany('Flexio\Modulo\OrdenesAlquiler\Models\OrdenVentaAlquiler','contrato_id');
    }

    public function ordenes_alquiler_por_facturar()
    {
        return $this->ordenes_alquiler()->where('ord_ventas_alquiler.estado','por_facturar');
    }

    public function ordenes_alquiler_facturadas_y_parciales()
    {
        return $this->ordenes_alquiler()->where(function($query){
            $query->where('ord_ventas_alquiler.estado','facturado_parcial');
            $query->orWhere('ord_ventas_alquiler.estado','facturado_completo');
        });
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

    public function getSaldoFacturarCurrencyAttribute()
    {
        return "$".number_format($this->saldo_facturar, 2, '.', ',');
    }

    public function getSaldoFacturarAttribute()
    {
        return $this->ordenes_alquiler_por_facturar->sum('total') - $this->total_facturado;
    }

    public function getTotalFacturadoAttribute()
    {
        return $this->ordenes_alquiler_facturadas_y_parciales->sum(function($orden_alquiler){
            return $orden_alquiler->facturas()->where(function($query){
                $query->where('fac_facturas.estado','por_cobrar')->orWhere('fac_facturas.estado','cobrado_parcial')->orWhere('fac_facturas.estado','cobrado_completo');
            })->sum('total');
        });
    }

    public function getTotalFacturadoCurrencyAttribute()
    {
        return "$".number_format($this->total_facturado, 2, '.', ',');
    }

    public function getDiasTranscurridosAttribute()
    {
        $fecha_inicio = $this->fecha_inicio;

        return $fecha_inicio->diffInDays();
    }

    public function getFechaFormatAttribute(){
        return $this->fecha_inicio->format('d/m/Y');
    }

    public function getFechaInicioFormatAttribute(){
        return $this->fecha_inicio->format('d/m/Y');
    }

    public function getFechaFinFormatAttribute(){
        return $this->fecha_fin != "" ? $this->fecha_fin->format('d/m/Y') : "";
    }

    public function getSaldoFacturarLabelAttribute()
    {
        $attrs = [
            'style'  => 'border: #d9534f solid 2px;color: #d9534f;width: 100%;background: transparent;padding: 2px 7px;text-align: center;font-weight: bold;'
        ];

        $html = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());
        return $html->setType('htmlLabel')->setAttrs($attrs)->setHtml($this->saldo_facturar_currency)->getSalida();
    }

    public function getTotalFacturadoLabelAttribute()
    {
        $attrs = [
            'style'  => 'border: #5cb85c solid 2px;color: #5cb85c;width: 100%;background: transparent;padding: 2px 7px;text-align: center;font-weight: bold;'
        ];

        $html = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());
        return $html->setType('htmlLabel')->setAttrs($attrs)->setHtml($this->total_facturado_currency)->getSalida();
    }

    public function getEnlaceAttribute()
    {
        return base_url('contratos_alquiler/editar/'.$this->uuid_contrato_alquiler);
    }

    public function getEnlaceBitacoraAttribute()
    {
        return base_url('contratos_alquiler/historial/'.$this->uuid_contrato_alquiler);
    }
    public function getFechaInicioAttribute($date)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date);//coloque esto porque el diseno no permite el ingreso de la fecha de inicio
        //return Carbon::createFromFormat('Y-m-d H:i:s', $date);
    }

    public function getFechaFinAttribute($date)
    {
        return $date != "0000-00-00 00:00:00" && $date != "" ? Carbon::createFromFormat('Y-m-d H:i:s', $date) : "";
    }

    public function setFechaInicioAttribute($date)
    {
        return $date != "0000-00-00 00:00:00" && $date != "" ? $this->attributes['fecha_inicio'] = Carbon::createFromFormat('d/m/Y', $date, 'America/Panama') : "0000-00-00 00:00:00";
    }

    public function setFechaFinAttribute($date)
    {

        return $date != "0000-00-00 00:00:00" && $date != "" ? $this->attributes['fecha_fin'] = Carbon::createFromFormat('d/m/Y', $date, 'America/Panama') : "0000-00-00 00:00:00";
    }

    public function cliente()
    {
        return $this->belongsTo('Flexio\Modulo\Cliente\Models\Cliente','cliente_id');
    }

    public function entregas()
    {
        return $this->morphMany('Flexio\Modulo\EntregasAlquiler\Models\EntregasAlquiler','entregable');
    }

    public function cotizable()
    {
        return $this->morphOne('Flexio\Modulo\CotizacionesAlquiler\Models\CotizablesAlquiler', 'cotizable');
    }

    public function items()
    {
        return $this->morphToMany('Flexio\Modulo\Inventarios\Models\Items', 'contratable', 'contratos_items', 'contratable_id', 'item_id')
                ->select('inv_items.id', 'inv_items.nombre', 'inv_items.tipo_id')
                ->withPivot(['id','categoria_id','item_id','cantidad','ciclo_id','tarifa','en_alquiler','devuelto','entregado','atributo_id','impuesto','descuento','cuenta_id']);
    }

    public function contratos_items()
    {
        return $this->morphMany('Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquilerItems', 'contratable');
    }

    public function centro_facturacion()
    {
        return $this->belongsTo('Flexio\Modulo\CentroFacturable\Models\CentroFacturable', 'centro_facturacion_id');
    }

    public function entregas_validas()
    {
        return $this->entregas()->where(function($entrega){
            $entrega->where('estado_id', 2)
                    ->orWhere('estado_id', 4);
        });
    }

    public function estado()
    {
        return $this->belongsTo('Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquilerCatalogos','estado_id');
    }

    public function corte_facturacion() {
        return $this->belongsTo('Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquilerCatalogos','corte_facturacion_id');
    }

    public function facturar_contra_entrega() {
        return $this->belongsTo('Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquilerCatalogos','facturar_contra_entrega_id');
    }

    public function calculo_costo_retorno() {
        return $this->belongsTo('Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquilerCatalogos','calculo_costo_retorno_id');
    }

    public function facturas()
    {
        return $this->morphToMany('Flexio\Modulo\FacturasVentas\Models\FacturaVenta','fac_facturable')
                ->withPivot('empresa_id');
    }

    public function facturas_validas()
    {
        return $this->facturas->whereIn('estado',['por_cobrar','cobrado_parcial','cobrado_completo']);
    }

    public function empresa()
    {
        return $this->belongsTo('Flexio\Modulo\Empresa\Models\Empresa','empresa_id');
    }

    public function scopeDeCodigo($query, $codigo = '')
    {
        return $query->where('codigo', 'like', '%'.$codigo.'%');
    }

    public function scopeDesde($query, $fecha_desde)
    {
        return $query->whereDate('fecha_inicio','>=',Carbon::createFromFormat('d/m/Y', $fecha_desde)->format('Y-m-d'));
    }

    public function scopeHasta($query, $fecha_hasta)
    {
        return $query->whereDate('fecha_inicio','<=',Carbon::createFromFormat('d/m/Y', $fecha_hasta)->format('Y-m-d'));
    }
    function scopeDeEmpresa($query, $clause){
    	return $query->where('empresa_id','=',$clause['empresa_id']);
    }
    function scopeEstadoValido($query){
    	return $query->whereHas('estado', function ($query) {
    		$query->where('tipo', '=', 'estado')->where('valor', 'LIKE', '%vigente%');
    	});
    }
    public function comentario_timeline() {
        return $this->morphMany(Comentario::class,'comentable');
    }
    function documentos() {
        return $this->morphMany(Documentos::class, 'documentable');
    }

    //funciones del landing_page
    public function getIconoAttribute(){
      return 'fa fa-car';
    }

    public function landing_comments(){
       return $this->morphMany(Comentario::class,'comentable');
    }
       public function historial(){
	  return $this->hasMany(ContratosAlquilerHistorial::class,'contrato_id');
      }

       public static function boot() {
           parent::boot();
           static::updating(function($contrato) {


                $cambio = $contrato->getDirty();
                $original = $contrato->getOriginal();

                 if(isset($cambio['estado_id'])){

                    $catalogo = ContratosAlquilerCatalogos::where("id","=",$original['estado_id'])->get();
                    $contrato->load("estado");
                    $descripcion = "<b style='color:#0080FF; font-size:15px;'>Cambio de estado en el contrato</b></br></br>";
                    $descripcion .= "Estado actual: ".$contrato->estado->nombre.'</br></br>';
                    $descripcion .= "Estado anterior: ".$catalogo[0]->nombre;

                     $create = [
                        'codigo' => $contrato->codigo,
                        'usuario_id' => $contrato->created_by,
                        'empresa_id' => $contrato->empresa_id,
                        'contrato_id'=> $contrato->id,
                        'tipo'   => "actualizado",
                      'descripcion' => $descripcion
                    ];
                    ContratosAlquilerHistorial::create($create);
                     return $contrato;
                 }


           });

           static::created(function($contrato){

                $create = [
                      'codigo' => $contrato->codigo,
                      'usuario_id' => $contrato->created_by,
                      'empresa_id' => $contrato->empresa_id,
                      'contrato_id'=> $contrato->id,
                      'tipo'   => "creado",
                      'descripcion' => "<b style='color:#0080FF; font-size:15px;'>Creó contrato de alquiler</b></br></br>No. ".$contrato->codigo."</br></br>Estado: Por aprobar"
                 ];
                 ContratosAlquilerHistorial::create($create);
                 return $contrato;
            });

       }
 }
