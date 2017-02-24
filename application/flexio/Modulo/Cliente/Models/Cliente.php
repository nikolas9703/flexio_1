<?php
namespace Flexio\Modulo\Cliente\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Contratos\Models\Contrato as Contrato;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\FacturasVentas\Models\FacturaVenta as FacturaVenta;
use Flexio\Modulo\Cobros\Models\Cobro as Cobro;
use Flexio\Modulo\NotaCredito\Models\NotaCredito as NotaCredito;
use Flexio\Modulo\ClientesAbonos\Models\ClienteAbono;
use Flexio\Library\Util\GenerarCodigo;
use Flexio\Modulo\CentroFacturable\Models\CentroFacturable;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\Documentos\Models\Documentos;
use Flexio\Modulo\Cliente\Models\Asignados;
use Flexio\Modulo\Cliente\Models\Telefonos;
use Flexio\Modulo\Cliente\Models\Correos;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;

class Cliente extends Model {

    use RevisionableTrait;
    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf =['codigo', 'nombre', 'empresa_id', 'telefono', 'correo', 'web', 'direccion', 'comentario', 'credito_limite', 'tipo_identificacion', 'identificacion', 'toma_contacto_id', 'letra', 'exonerado_impuesto', 'tipo', 'categoria', 'estado','credito_favor', 'creado_por','detalle_identificacion','retiene_impuesto','lista_precio_venta_id','lista_precio_alquiler_id','termino_pago'];

    protected $table = 'cli_clientes';
    protected $fillable = ['codigo', 'nombre', 'empresa_id', 'telefono', 'correo', 'web', 'direccion', 'comentario', 'credito_limite', 'tipo_identificacion', 'identificacion', 'toma_contacto_id', 'letra', 'exonerado_impuesto', 'tipo', 'categoria', 'estado','credito_favor', 'creado_por','detalle_identificacion','retiene_impuesto','lista_precio_venta_id','lista_precio_alquiler_id','termino_pago'];
    protected $guarded = ['id'];
    protected $appends = ['saldo_pendiente','icono','enlace'];
    protected $casts = [
        "detalle_identificacion" => "array",
        "credito_favor" => "real"
    ];

    public function __construct(array $attributes = array()) {
        $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_cliente' => Capsule::raw("ORDER_UUID(uuid())")
                )), true);
        parent::__construct($attributes);
    }
    public static function boot() {
        parent::boot();
    }
    public function toArray() {
        $array = parent::toArray();
        return $array;
    }

    public static function registrar() {
        return new static;
    }

    //mutators

    //se le puede cambiar el estado a inactivo return bolean
    public function getInactivableAttribute()
    {
        return $this->credito_favor == 0 && $this->saldo_pendiente == 0 && count($this->estadoFacturaValidate) == 0;
    }

    function setCodigoAttribute($value) {
      return $this->attributes['codigo'] = GenerarCodigo::setCodigo('CUS', $value);
    }

    public function setCreditoLimiteAttribute($value)
    {
        return $this->attributes['credito_limite'] = str_replace(",", "", $value);
    }

    public function getCreatedAtAttribute($date) {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d H:i:s');
    }

    public function getNombreCompletoEnlaceAttribute() {
        $attrs = [
            'href'  => $this->enlace,
            'class' => 'link'
        ];

        $html = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());
        return $html->setType('htmlA')->setAttrs($attrs)->setHtml($this->nombre)->getSalida();
    }

    public function getEnlaceAttribute() {
        return base_url('clientes/ver/'.$this->uuid_cliente);
    }

    public function devoluciones(){

        return $this->hasMany('Flexio\Modulo\Devoluciones\Models\Devolucion', 'cliente_id');

    }

  /*  public function getCreditoFavorAttribute() {
      dd($this->attributes['codigo']);
        //$abonos = $this->total_credito_cliente()->sum('monto_abonado');
        //nota en las trasaciones de notas y devuliciones
        //debe se sumar al credito del cliente y quitar la sumatoria de aqui
       $notas_credito = $this->nota_credito()->where('venta_nota_creditos.estado','aprobado')->sum('total');

        $devoluciones = $this->devoluciones()->where('dev_devoluciones.estado','aprobada')->sum('total');
        dd($this);
       return (float)  $notas_credito + $devoluciones + $this->attributes['credito_favor'];
       //return 2;

    }*/

    /*public function getCreditoAttribute() {

        $abonos = $this->total_credito_cliente()->sum('monto_abonado');
        $notas_credito = $this->nota_credito()->where('venta_nota_creditos.estado','aprobado')->sum('total');
        $devoluciones = $this->devoluciones()->where('dev_devoluciones.estado','aprobada')->sum('total');

        return (float) $abonos + $notas_credito + $devoluciones - $this->credito_usado;
    }*/

    public function getSaldoPendienteAttribute() {
        return $this->total_saldo_pendiente();
        //return number_format($this->total_saldo_pendiente(), 2, '.', ',');
    }

    public function getUuidClienteAttribute($value) {
        return strtoupper(bin2hex($value));
    }

    public function getUuidAttribute() {
        return $this->uuid_cliente;
    }


    public function facturasValidas() {
        return $this->hasMany(FacturaVenta::class, 'cliente_id')->whereNotIn('estado', array('anulada', 'cobrado_completo', 'por_aprobar'));
    }

    //Relationships
    public function facturas() {
        return $this->hasMany(FacturaVenta::class, 'cliente_id');
    }

    public function facturas_por_cobrar() {
        return $this->hasMany(FacturaVenta::class, 'cliente_id')->where(function($factura){
            $factura->whereIn('fac_facturas.estado', ['por_cobrar', 'cobrado_parcial']);
        });
    }

    /**
     * //@method centro_facturable()
     * Obtiene los centros facturables associados con el cliente
     * @return \Illuminate\Database\Eloquent\Relationships\HasMany
     */

    public function centro_facturable() {
        return $this->hasMany(CentroFacturable::class,'cliente_id')->where('cli_centros_facturacion.eliminado','0');
    }

    public function clientes_asignados() {
    	return $this->hasMany(Asignados::class,'cliente_id');
    }
    public function telefonos_asignados() {
        return $this->hasMany(Telefonos::class,'cliente_id');
    }
    public function correos_asignados() {
        return $this->hasMany(Correos::class,'cliente_id');
    }
    function contrato() {
        return $this->hasMany(Contrato::class, 'cliente_id');
    }

    function nota_credito() {
        return $this->hasMany(NotaCredito::class, 'cliente_id');
    }

    public function anticipos()
    {
        return $this->morphMany('Flexio\Modulo\Anticipos\Models\Anticipo', 'anticipable')->where('estado','aprobado');
    }

    public function anticipo_cliente(){
        return $this->morphMany('Flexio\Modulo\Anticipos\Models\Anticipo', 'anticipable')->where('estado','aprobado')->has('orden_venta','<',1)->has('contrato','<',1);
    }

    //cambiar a relacion morph
    public function cobros_cliente() {
        return $this->hasMany(Cobro::class, 'cliente_id');
    }

    //scopes

    function scopeDeEmpresa($query, $empresa_id) {
        return self::where('empresa_id', '=', $empresa_id);
    }

    // functiones
    public function conFacturaParaCobrar() {

        return $this->hasMany(FacturaVenta::class, 'cliente_id')->where('estado', '=', 'por_cobrar');
    }

    public static function conFacturas($empresa_id) {
        return self::whereHas('facturas', function($query) use($empresa_id) {
                    $query->where('fac_facturas.estado', 'por_cobrar');
                    $query->where('empresa_id', '=', $empresa_id);
                })->get();
    }

    public static function conFacturasVer($empresa_id) {
    return self::whereHas('facturas', function($query) use($empresa_id) {
        $query->whereIn('fac_facturas.estado', ['cobrado_parcial', 'cobrado_completo']);
        $query->where('empresa_id', '=', $empresa_id);
    })->get();
}
    public function estadoFacturaValidate() {
        return $this->facturas()->whereIn('fac_facturas.estado', ['cobrado_parcial', 'por_cobrar', 'por_aprobar']);
    }
    public function total_saldo_pendiente() {
        $total_facturas = $this->facturas_por_cobrar()->sum('total');
        $total_cobrado = $this->cobros_cliente()->whereHas('factura_cobros',function($fac){
            $fac->whereIn('fac_facturas.estado',['por_cobrar','cobrado_parcial']);
            $fac->where('transaccion',1);
        })->sum('monto_pagado');
        return ($total_facturas - $total_cobrado);
    }

    public function total_credito_cliente() {
        return $this->hasMany(ClienteAbono::class, 'cliente_id', 'id');
    }

    public function uuid() {
        return $this->uuid_cliente;
    }

    public static function findByUuid($uuid) {
        return self::where('uuid_cliente', hex2bin($uuid))->first();
    }

    public static function findByUuids($uuid = array()) {
        return self::whereIn('uuid_cliente', hex2bin($uuid))->get();
    }

    public static function selectExportClient($clause = array()) {

        return self::whereIn('uuid_cliente', $clause)->get();
    }


    function clientesConContratos($clause = []) {
        return self::whereHas('contrato', function($query) use($clause) {
                    $query->where('empresa_id', '=', $clause['empresa_id']);
                })->get();
    }

    function clientesConNotaCredito($clause = []) {
        return self::whereHas('nota_credito', function($query) use($clause) {
                    $query->where('empresa_id', '=', $clause['empresa_id']);
                })->get();
    }
    public function comentario_timeline() {
    	return $this->morphMany(Comentario::class,'comentable');
    }

    ///functiones del landing page
    public function getIconoAttribute() {
     return 'fa fa-line-chart';
    }
    public function landing_comments() {
        return $this->morphMany(Comentario::class,'comentable');
    }
    function documentos() {
    	return $this->morphMany(Documentos::class, 'documentable');
    }
   public static function getIdentificacionClientes($clause) {
        return self::where(function ($query) use($clause) {
            $query->where('empresa_id', '=', $clause['empresa_id'])
                ->where('identificacion', '=', $clause['identificacion']);
        });
    }

}
