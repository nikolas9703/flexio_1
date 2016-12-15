<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Documentos\Models\Documentos;
use Carbon\Carbon as Carbon;
use Flexio\Politicas\PoliticableTrait;


class Pagos_orm extends Model {

    use PoliticableTrait;
    //propiedades politicas
    protected $politica = 'pago';

    protected $table = 'pag_pagos';
    protected $fillable = ['uuid_pago','codigo', 'proveedor_id', 'empresa_id', 'fecha_pago', 'estado', 'monto_pagado', 'cuenta_id', 'referencia', 'depositable_id', 'depositable_type'];
    protected $guarded = ['id', 'uuid_cobro'];
    protected $codeIgniter;
    protected $appends = ['tipo_deposito','icono','enlace'];

    protected $deposito = ["Flexio\Modulo\Contabilidad\Models\Cuentas"=>'banco','Flexio\Modulo\Cajas\Models\Cajas'=>'caja'];

    public function __construct(array $attributes = array()) {
        $this->codeIgniter = & get_instance();
        $this->setRawAttributes(array_merge($this->attributes, array('uuid_pago' => Capsule::raw("ORDER_UUID(uuid())"))), true);
        parent::__construct($attributes);
        //Cargando Modelos
        $this->codeIgniter->load->model("pedidos/Pedidos_orm");
    }

    public function landing_comments()
    {
        return $this->hasMany('Flexio\Modulo\Comentario\Models\Comentario','comentable_id')
        ->where('comentarios.comentable_type','Flexio\\Modulo\\Pagos\\Models\\Pagos');
    }

    public function getFechaPagoAttribute($date) {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d-m-Y');
    }

    public function getTipoDepositoAttribute(){
        if(isset($this->deposito[$this->depositable_type])){
           return $this->deposito[$this->depositable_type];
        }
        return 'banco';
    }

    public function getUuidPagoAttribute($value) {
        return strtoupper(bin2hex($value));
    }

    public function empresa()
    {
        return $this->belongsTo('Flexio\Modulo\Empresa\Models\Empresa', 'empresa_id');
    }

    public function facturas()
    {
        return $this->morphedByMany('Flexio\Modulo\FacturasCompras\Models\FacturaCompra','pagable', 'pag_pagos_pagables','pago_id')
                ->withPivot('monto_pagado','empresa_id')->withTimestamps();
    }

    public function planillas() {
        return $this->morphedByMany('Flexio\Modulo\Planilla\Models\Planilla', 'pagable', 'pag_pagos_pagables', 'pago_id')
                        ->withPivot('monto_pagado', 'empresa_id')->withTimestamps();
    }

    public function pagos_extraordinarios()
    {
        return $this->belongsToMany('Flexio\Modulo\Comisiones\Models\Comisiones', 'pag_pagos_pagables', 'pago_id', 'pagable_id')
                ->where("pagable_type", "Flexio\Modulo\Comisiones\Models\Comisiones")
                ->withPivot('monto_pagado','empresa_id')->withTimestamps();
    }
    
    public function colaborador()
    {
        return $this->belongsTo('Flexio\Modulo\Colaboradores\Models\Colaboradores', 'proveedor_id');
    }
    public function pagos_pagables() {
        return $this->hasMany('Pago_pagables_orm', 'pago_id');
    }

    public function total_pagado() {
        return $this->hasMany('Pago_pagables_orm', 'pago_id')->sum('monto_pagado');
    }

    public function metodo_pago()
    {
        return $this->hasMany('Flexio\Modulo\Pagos\Models\PagosMetodos','pago_id');
    }

    public function catalogo_estado() {
        return $this->belongsTo('Pago_catalogos_orm', 'estado', 'etiqueta')->where('tipo', '=', 'etapa3');
    }

    public function proveedor() {
        return $this->belongsTo('Proveedores_orm', 'proveedor_id');
    }

    public function scopeDeEmpresa($query, $empresa_id) {
        return $query->where("empresa_id", $empresa_id);
    }

    public function scopeDeCaja($query, $caja_id) {
        return $query->where("depositable_id", $caja_id);
    }

    public function scopeDeFechaDesde($query, $fechaDesde) {
        return $query->whereDate("fecha_pago", ">=", date("Y-m-d", strtotime($fechaDesde)));
    }

    public function scopeDeFechaHasta($query, $fechaHasta) {
        return $query->whereDate("fecha_pago", "<=", date("Y-m-d", strtotime($fechaHasta)));
    }

    public function scopeDeProveedor($query, $proveedor_id) {
        $aux = is_numeric($proveedor_id) ? $proveedor_id : Proveedores_orm::where("uuid_proveedor", hex2bin($proveedor_id))->first()->id;
        return $query->where("proveedor_id", $aux);
    }

    public function scopeDeOrdenDeCompra($query, $orden_compra_id)
    {
        return $query->whereHas("facturas", function($factura) use ($orden_compra_id){
            $factura->whereHas("orden", function($orden) use ($orden_compra_id){
                $orden->where("id", $orden_compra_id);
            });
        });
    }

    public function scopeDeFacturaDeCompra($query, $factura_compra_id)
    {
        return $query->whereHas("facturas", function($factura) use ($factura_compra_id){
            $factura->where("faccom_facturas.id", $factura_compra_id);
        });
    }

    public function scopeDeEstado($query, $estado){
        return $query->where("estado", $estado);
    }

    public function scopeDeMontoMin($query, $montoMin) {
        return $query->where("monto_pagado", ">=", $montoMin);
    }

    public function scopeDeMontoMax($query, $montoMax) {
        return $query->where("monto_pagado", "<=", $montoMax);
    }

    public function scopeDeFormaPago($query, $formaPago) {
        return $query->whereHas("metodo_pago", function($q) use ($formaPago) {
                    $q->where("tipo_pago", $formaPago);
                });
    }

    public function scopeDeDocumentoPago($query, $numeroDocumento) {
      return  $query->whereHas("facturas", function($factura)  use ($numeroDocumento) {
               $factura->where("factura_proveedor", $numeroDocumento);
       })->orWhereHas("planillas", function($planilla)  use ($numeroDocumento) {
                 $planilla->where("codigo", $numeroDocumento);
        });

    }


    public function scopeDeTipo($query, $tipo) {
        if ($tipo === "planilla") {
            return $query->where("formulario", $tipo);
        } else {
            return $query->where(function($q) {
                        $q->where("formulario", "factura")
                                ->orWhere("formulario", "proveedor");
                    });
        }
    }

    public function scopeDeBanco($query, $banco) {
        return $query->whereHas("metodo_pago", function($q) use ($banco) {
                    $q->where(Capsule::raw('CONVERT(referencia USING utf8)'), "like", "%\"nombre_banco_ach\":\"$banco\"%");
                });
    }

    public function scopeDePedido($pagos, $pedido_id) {

        return $pagos->whereHas("facturas", function($factura) use($pedido_id) {

            $factura->whereHas("orden", function($orden) use($pedido_id) {

                $orden->whereHas("pedido", function($pedido) use($pedido_id) {

                    $pedido->where("ped_pedidos.id",  $pedido_id);

                });
            });
        });

    }

    public function getNumeroDocumentoAttribute()
    {

    	return $this->codigo;
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
    public function getEnlaceAttribute()
    {
    	return base_url("pagos/ver/".$this->uuid_pago);
    }
    public function getIconoAttribute(){
        return 'fa fa-shopping-cart';
    }
    function documentos(){
    	return $this->morphMany(Documentos::class, 'documentable');
    }

}
