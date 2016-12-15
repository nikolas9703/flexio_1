<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Contratos\Models\Contrato as Contrato;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\FacturasVentas\Models\FacturaVenta as FacturaVenta;
use Flexio\Modulo\Cobros\Models\Cobro as Cobro;
use Flexio\Modulo\NotaCredito\Models\NotaCredito as NotaCredito;
use Flexio\Modulo\ClientesAbonos\Models\ClienteAbono;
use Flexio\Modulo\CentrosContables\Models\CentrosContables;
use Flexio\Modulo\Cliente\Models\Telefonos;
use Flexio\Modulo\Cliente\Models\Correos;
use Flexio\Modulo\ConfiguracionVentas\Models\CategoriaClientes;
use Flexio\Modulo\ConfiguracionVentas\Models\TipoClientes;
use Flexio\Modulo\Cliente\Models\Cliente;

class Cliente_orm extends Model {

    protected $table = 'cli_clientes';
    protected $fillable = ['codigo', 'nombre', 'empresa_id', 'telefono', 'correo', 'web', 'direccion', 'comentario', 'credito', 'tipo_identificacion', 'identificacion', 'toma_contacto_id', 'letra', 'exonerado_impuesto', 'tipo', 'categoria', 'estado'];
    protected $guarded = ['id', 'uuid_cliente'];
    protected $appends = ['saldo_pendiente'];

    public function __construct(array $attributes = array()) {
        $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_cliente' => Capsule::raw("ORDER_UUID(uuid())")
                )), true);
        parent::__construct($attributes);
    }

    public function toArray() {
        $array = parent::toArray();
        //$array['saldo_pendiente'] = number_format($this->total_saldo_pendiente(), 2, '.', ',');
        return $array;
    }

    //mutators
    public function getCreatedAtAttribute($date) {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d H:i:s');
    }

    public function devoluciones(){

        return $this->hasMany('Flexio\Modulo\Devoluciones\Models\Devolucion', 'cliente_id');

    }

    public function getCreditoFavorAttribute() {

          //$abonos = $this->total_credito_cliente()->sum('monto_abonado');
          //nota en las trasaciones de notas y devuliciones
          //debe se sumar al credito del cliente y quitar la sumatoria de aqui
          $notas_credito = $this->nota_credito()->where('venta_nota_creditos.estado','aprobado')->sum('total');
          $devoluciones = $this->devoluciones()->where('dev_devoluciones.estado','aprobada')->sum('total');
          return (float)  $notas_credito + $devoluciones + $this->attributes['credito_favor'];
      }

    /*public function getCreditoAttribute() {

        $abonos = $this->total_credito_cliente()->sum('monto_abonado');
        $notas_credito = $this->nota_credito()->where('venta_nota_creditos.estado','aprobado')->sum('total');
        $devoluciones = $this->devoluciones()->where('dev_devoluciones.estado','aprobada')->sum('total');

        return (float) $abonos + $notas_credito + $devoluciones - $this->credito_usado;

    }*/

    public function getSaldoPendienteAttribute() {
        return number_format($this->total_saldo_pendiente(), 2, '.', ',');
    }

    public function getUuidClienteAttribute($value) {
        return strtoupper(bin2hex($value));
    }

    /* public function facturasHabilitadas(){
      return $this->hasMany(FacturaVenta::class,'cliente_id')->whereIn('estado',array('por_aprobar'));
      } */

      public function centros_facturable(){
          return $this->belongsToMany(CentrosContables::class,'cli_centros_facturacion','cliente_id','centro_id')
               ->withPivot('direccion')->withTimestamps();
      }

    public function facturasValidas() {
        return $this->hasMany(FacturaVenta::class, 'cliente_id')->whereNotIn('estado', array('anulada', 'cobrado_completo', 'por_aprobar'));
    }

    /* public function facturasCrear(){
      return $this->hasMany(FacturaVenta::class,'cliente_id')->where('estado','=','por_aprobar');
      } */

    //Relationships
    public function facturas() {
        return $this->hasMany(FacturaVenta::class, 'cliente_id');
    }

    public function facturas_por_cobrar() {
        return $this->hasMany(FacturaVenta::class, 'cliente_id')->where(function($factura){
            $factura->whereIn('fac_facturas.estado', ['por_cobrar', 'cobrado_parcial']);
        });
    }

    function contrato() {
        return $this->hasMany(Contrato::class, 'cliente_id');
    }

    function nota_credito() {
        return $this->hasMany(NotaCredito::class, 'cliente_id');
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

        return $this->hasMany(FacturaVenta::class, 'cliente_id')->whereIn('fac_facturas.estado', ['por_cobrar', 'cobrado_parcial']);
    }

    public static function conFacturas($empresa_id) {
        return self::whereHas('facturas', function($query) use($empresa_id) {
                    $query->whereIn('fac_facturas.estado', ['por_cobrar', 'cobrado_parcial']);
                    $query->where('empresa_id', '=', $empresa_id);
                })->get();
    }

    public static function conFacturasVer($empresa_id) {
        return self::whereHas('facturas', function($query) use($empresa_id) {
                    $query->whereIn('fac_facturas.estado', ['cobrado_parcial', 'cobrado_completo']);
                    $query->where('empresa_id', '=', $empresa_id);
                })->get();
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
        return $this->hasOne(ClienteAbono::class, 'cliente_id', 'id');
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

    static function lista_totales($clause = array()) {
        return self::where(function($query) use($clause) {
                    $query->where('empresa_id', '=', $clause['empresa_id']);
            if (isset($clause['nombre']))
                $query->where('nombre', 'like', "%" . $clause['nombre'] . "%");
            if (isset($clause['telefono']))
                $query->where('telefono', 'like', "%" . $clause['telefono'] . "%");
            if (isset($clause['correo']))
                $query->where('correo', 'like', "%" . $clause['correo'] . "%");
            if (isset($clause['tipo']))
                $query->where('tipo', 'like', "%" . $clause['tipo'] . "%");
            if (isset($clause['categoria']))
                $query->where('categoria', 'like', "%" . $clause['categoria'] . "%");
            if (isset($clause['tipo_identificacion']) && $clause['tipo_identificacion'] == 'natural'){
                $query->where('tipo_identificacion', 'like', "%" . $clause['tipo_identificacion'] . "%")
                    ->orWhere('tipo_identificacion', 'like', "%pasaporte%");
            }elseif (isset($clause['tipo_identificacion'])){
                $query->where('tipo_identificacion', 'like', "%" . $clause['tipo_identificacion'] . "%");
            }
            if (isset($clause['estado']))
                $query->where('estado', $clause['estado']);
            if (isset($clause['id']))
                $query->where('id', 'like', "%" . $clause['id'] . "%");
                })->count();
    }

    static function lista_totales_clientes($clause = array()) {
        return self::where(function($query) use($clause) {
                    $query->whereIn('uuid_cliente', $clause['uuid']);
                    if (isset($clause['nombre']))
                        $query->where('nombre', 'like', "%" . $clause['nombre'] . "%");
                    if (isset($clause['telefono']))
                        $query->where('telefono', 'like', "%" . $clause['telefono'] . "%");
                    if (isset($clause['correo']))
                        $query->where('correo', 'like', "%" . $clause['correo'] . "%");
                    if (isset($clause['id']))
                        $query->where('id', 'like', "%" . $clause['id'] . "%");
                })->count();
    }

    /**
     * function de listar y busqueda
     */
    public static function listar($clause = array(), $sidx = NULL, $sord = NULL, $limit = NULL, $start = NULL) {
        $clientes = self::with(array('telefonos_asignados','correos_asignados', 'estados_asignados'))->where(function($query) use($clause) {
                    $query->where('empresa_id', '=', $clause['empresa_id']);
                    if (isset($clause['nombre']))
                        $query->where('nombre', 'like', "%" . $clause['nombre'] . "%");
                    if (isset($clause['telefono']))
                        $query->where('telefono', 'like', "%" . $clause['telefono'] . "%");
                    if (isset($clause['correo']))
                        $query->where('correo', 'like', "%" . $clause['correo'] . "%");
                   if (isset($clause['tipo']))
                       $query->where('tipo', 'like', "%" . $clause['tipo'] . "%");
                   if (isset($clause['categoria']))
                       $query->where('categoria', 'like', "%" . $clause['categoria'] . "%");
                   if (isset($clause['tipo_identificacion']) && $clause['tipo_identificacion'] == 'natural'){
                       $query->where(function ($tipo) use($clause){
                           $tipo->where('tipo_identificacion', 'like', "%" . $clause['tipo_identificacion'] . "%")
                                ->orWhere('tipo_identificacion', 'like', "%pasaporte%");
                       });
                   }elseif (isset($clause['tipo_identificacion'])){
                       $query->where('tipo_identificacion', 'like', "%" . $clause['tipo_identificacion'] . "%");
                   }
                   if (isset($clause['estado']))
                      $query->where('estado', $clause['estado']);
                   if (isset($clause['id']))
                        $query->where('id', 'like', "%" . $clause['id'] . "%");
                });
        if ($sidx != NULL && $sord != NULL)
            $clientes->orderBy($sidx, $sord);
        if ($limit != NULL)
            $clientes->skip($start)->take($limit);

        return $clientes->get();
    }

    /**
     * function de listar clientes agrupados.
     */
    public static function listar_clientes($clause = array(), $sidx = NULL, $sord = NULL, $limit = NULL, $start = NULL) {
        $clientes = self::where(function($query) use($clause) {
                    if (isset($clause['uuid']))
                        $query->whereIn('uuid_cliente', $clause['uuid']);
                    if (isset($clause['nombre']))
                        $query->where('nombre', 'like', "%" . $clause['nombre'] . "%");
                    if (isset($clause['telefono']))
                        $query->where('telefono', 'like', "%" . $clause['telefono'] . "%");
                    if (isset($clause['correo']))
                        $query->where('correo', 'like', "%" . $clause['correo'] . "%");
                });
        if ($sidx != NULL && $sord != NULL)
            $clientes->orderBy($sidx, $sord);
        if ($limit != NULL)
            $clientes->skip($start)->take($limit);

        return $clientes->get();
    }

    public static function operations($clause = array(), $sidx = NULL, $sord = NULL, $limit = NULL, $start = NULL) {
        $clientes = self::where(function($query) use($clause) {
                    $query->whereIn('uuid_cliente', $clause);
                    if (isset($clause['nombre']))
                        $query->where('nombre', 'like', "%" . $clause['nombre'] . "%");
                    if (isset($clause['telefono']))
                        $query->where('telefono', 'like', "%" . $clause['telefono'] . "%");
                    if (isset($clause['correo']))
                        $query->where('correo', 'like', "%" . $clause['correo'] . "%");
                });
        if ($sidx != NULL && $sord != NULL)
            $clientes->orderBy($sidx, $sord);
        if ($limit != NULL)
            $clientes->skip($start)->take($limit);

        return $clientes->get();
    }

    public static function selectExportClient($clause = array()) {

        return self::with(array('tipo_cliente','categoria_cliente','telefonos_asignados','correos_asignados'))->whereIn('uuid_cliente', $clause)->get();
    }

    public static function searchClient($clause = array()) {
        $clientes = self::where(function($query) use($clause) {
                   //$query->select('uuid_cliente');
                    if (isset($clause['nombre']))
                        $query->where('nombre', 'like', "%" . $clause['nombre'] . "%");
                    if (isset($clause['telefono']))
                        $query->where('telefono', 'like', "%" . $clause['telefono'] . "%");
                    if (isset($clause['correo']))
                        $query->where('correo', 'like', "%" . $clause['correo'] . "%");
                });

        return $clientes->get();
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
    public function telefonos_asignados() {
        return $this->hasMany(Telefonos::class,'cliente_id');
    }
    public function correos_asignados() {
        return $this->hasMany(Correos::class,'cliente_id');
    }

    public function tipo_cliente(){
        return $this->hasMany(TipoClientes::class, 'id', 'tipo');
    }

    public function categoria_cliente(){
        return $this->hasMany(CategoriaClientes::class, 'id', 'categoria');
    }

    public function estados_asignados() {
        return $this->belongsTo('Catalogo_orm','estado','etiqueta');
    }
    function present(){
        return new \Flexio\Modulo\Cliente\Presenter\ClientesPresenter($this);
    }

    function getClientesEstadoIP($clause) {
        return Cliente::where(function ($query) use($clause) {
            $query->where('empresa_id', '=', $clause['empresa_id'])
                ->where('estado', '!=', 'por_aprobar')
                ->where('estado', '!=', 'inactivo');
        });
    }

}
