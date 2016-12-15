<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\FacturasCompras\Models\FacturaCompra;
use Flexio\Modulo\SubContratos\Models\SubContrato;


class Pagos_contratos_orm extends Model {

    protected $table = 'pag_pagos';
    protected $fillable = ['codigo', 'proveedor_id', 'empresa_id', 'fecha_pago', 'estado', 'monto_pagado', 'cuenta_id', 'referencia'];
    protected $guarded = ['id', 'uuid_cobro'];
    protected $codeIgniter;

    public function __construct(array $attributes = array()) {
        $this->codeIgniter = & get_instance();
        $this->setRawAttributes(array_merge($this->attributes, array('uuid_pago' => Capsule::raw("ORDER_UUID(uuid())"))), true);
        parent::__construct($attributes);
        //Cargando Modelos
        $this->codeIgniter->load->model("pedidos/Pedidos_orm");
        //$this->codeIgniter->load->modal("facturas_compras/Facturas_compras_orm");
    }

    public function getFechaPagoAttribute($date) {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d-m-Y');
    }

    public function getUuidPagoAttribute($value) {
        return strtoupper(bin2hex($value));
    }

    public function facturas() {
        return $this->morphedByMany('Facturas_compras_orm', 'pagable', 'pag_pagos_pagables', 'pago_id')
                        ->withPivot('monto_pagado', 'empresa_id')->withTimestamps();
    }
   /* public function factura(){
        return $this->belongsTo('Facturas_compras_orm', 'operacion');
    }*/

    public function planillas() {
        return $this->morphedByMany('Planilla_orm', 'pagable', 'pag_pagos_pagables', 'pago_id')
                        ->withPivot('monto_pagado', 'empresa_id')->withTimestamps();
    }

    public function pagos_pagables() {
        return $this->hasMany('Pago_pagables_contratos_orm', 'pago_id');
    }

    public function total_pagado() {
        return $this->hasMany('Pago_pagables_contratos_orm', 'pago_id')->sum('monto_pagado');
    }

    public function metodo_pago() {
        return $this->hasMany('Pago_metodos_pago_contratos_orm', 'pago_id');
    }

    public function catalogo_estado() {
        return $this->belongsTo('Pago_catalogos_contratos_orm', 'estado', 'etiqueta')->where('tipo', '=', 'etapa3');
    }

    public function proveedor() {
        return $this->belongsTo('Proveedores_orm', 'proveedor_id');
    }
      public function operacion() {
        return $this->morphTo();
    }

    public function scopeDeEmpresa($query, $empresa_id) {
        return $query->where("empresa_id", $empresa_id);
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

    public function scopeDePedidos($pagos, $uuid_pedido){
        return $pagos->whereHas("facturas", function($facturas)use($uuid_pedido) {
                    $facturas->whereHas("orden", function($orden)use($uuid_pedido) {
                        $orden->whereHas("pedido", function($pedido)use($uuid_pedido) {
                            $pedido->where("uuid_pedido", hex2bin($uuid_pedido));
                        });
                    });
                });
    }
    public function scopeDeContratos($pagos){
        return $pagos->whereHas("facturas", function($facturas){
                     $facturas->whereHas("contratos", function($contratos){
                       $contratos->where("empresa_id", "1");
                        // $contratos->get();
                     });
        });
    }

}
