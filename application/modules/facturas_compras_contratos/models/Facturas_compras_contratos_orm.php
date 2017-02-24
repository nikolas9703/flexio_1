<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\FacturasCompras\Models\FacturaCompra;
use Flexio\Modulo\FacturasVentas\Models\FacturaVenta;

class Facturas_compras_contratos_orm extends Model {

    protected $table = 'faccom_facturas';
    protected $fillable = ['codigo', 'proveedor_id', 'empresa_id', 'fecha_hasta', 'fecha_desde', 'factura_proveedor', 'estado', 'created_by', 'comentario', 'termino_pago', 'fecha_termino_pago', 'item_precio_id', 'subtotal', 'impuestos', 'total', 'bodega_id', 'centro_contable_id', 'cotizacion_id', 'referencia', 'orden_venta_id'];
    protected $guarded = ['id', 'uuid_factura'];
    protected $appends = ['is_refactura'];
    protected $codeIgniter;

    public function __construct(array $attributes = array()) {
        $this->codeIgniter = & get_instance();

        $this->setRawAttributes(
                array_merge(
                        $this->attributes, array(
            'uuid_factura' => Capsule::raw("ORDER_UUID(uuid())")
                        )
                ), true
        );

        parent::__construct($attributes);

        //Cargando Modelos
        $this->codeIgniter->load->model("proveedores/Proveedores_orm");
        $this->codeIgniter->load->model("centros/Centros_orm");
        $this->codeIgniter->load->model("facturas/Factura_catalogo_orm");
        $this->codeIgniter->load->model("facturas_compras_contratos/Facturas_compras_contratos_items_orm");
        $this->codeIgniter->load->model("bodegas/Bodegas_orm");
        $this->codeIgniter->load->model("usuarios/Usuario_orm");
        $this->codeIgniter->load->model("ordenes/Ordenes_orm");
        //$this->codeIgniter->load->model("pedidos/Pedidos_orm");
    }

    public static function findByUuid($uuid) {
        return strtoupper(bin2hex($uuid));
        //return self::where('uuid_pedido', hex2bin($uuid))->first();
    }

    public function getUuidFacturaAttribute($value) {
        return strtoupper(bin2hex($value));
    }

    public function getCreatedAtAttribute($value) {
        return date("d-m-Y", strtotime($value));
    }

    public function getUpdatedAtAttribute($value) {
        return date("d-m-Y", strtotime($value));
    }

    public function getFechaDesdeAttribute($date) {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d-m-Y');
    }

    public function getFechaHastaAttribute($date) {
        return '';
        //return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d-m-Y');
    }

    public function getIsRefacturaAttribute() {
        return $this->refactura->count() > 0;
    }

    public function getPagableAttribute()
    {
        //14 => Por pagar
        //15 => Pagada parcial
        return $this->estado_id == '14' || $this->estado_id == '15';
    }

    public function getCodigoEnlaceAttribute()
    {
        $attrs = [
            "class" => "link",
            "href"  => base_url("facturas_compras_contratos/ver/".$this->uuid_factura)
        ];
        $html = new Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());
        return $html->setType("htmlA")->setAttrs($attrs)->setHtml($this->codigo)->getSalida();
    }

    public function proveedor() {
        return $this->belongsTo('Proveedores_orm', 'proveedor_id', 'id');
    }

    public function bodega() {
        return $this->belongsTo('Bodegas_orm', 'bodega_id', 'id');
    }

    public function comprador() {
        return $this->belongsTo('Usuario_orm', 'created_by', 'id');
    }

    //solo los pagos que tienen estado aplicado son los que se toman en cuenta
    //para la relacion dejando por fuera los pagos con estados "por aplicar"
    //y "anulado"
    public function pagos() {
        return $this->morphToMany('Pagos_orm', 'pagable', 'pag_pagos_pagables', '', "pago_id")
                        //->where("pag_pagos.estado", "aplicado")
                        ->withPivot('monto_pagado', 'empresa_id')->withTimestamps();
    }

    public function pagos_aplicados() {
        return $this->morphToMany('Pagos_orm', 'pagable', 'pag_pagos_pagables', '', "pago_id")
                        ->where("pag_pagos.estado", "aplicado")
                        ->withPivot('monto_pagado', 'empresa_id')->withTimestamps();
    }

    public function metodo_pago() {

    }

    public function operacion() {
        $this->morphTo();
    }

    public function scopeDePedidos($query, $uuid)
   {
       return $query->whereHas("orden", function($q)use($uuid){
           $q->whereHas("pedido", function($q1)use($uuid){
               $q1->where("uuid_pedido",  hex2bin($uuid));
                 // ->where("estado_id","=","2");
           });
       });
   }

    public function pedidos() {
        return $this->belongsTo('Pedidos_orm', 'uuid_pedido', 'uuid_pedido');
    }

    public function centro_contable() {
        return $this->belongsTo('Centros_orm', 'centro_contable_id', 'id');
    }

    public function estado() {
        return $this->belongsTo('Factura_catalogo_orm', 'estado_id', 'id');
    }

    public function facturas_compras_items() {
        return $this->hasMany('Facturas_compras_contratos_items_orm', 'factura_id');
    }

    public function items() {
        return $this->belongsToMany('Items_orm', 'faccom_facturas_items', 'factura_id', 'item_id')
                        ->withPivot("categoria_id", "cantidad", "unidad_id", "precio_unidad", "impuesto_id", "impuesto_nombre", "impuesto_cuenta", "descuento", "cuenta_id", "total", "subtotal", "descuentos", "impuestos", "cuenta_id");
    }

    public function scopeDeEmpresa($query, $empresa_id) {
        return $query->where("empresa_id", $empresa_id);
    }

    public function scopeParaPagos($query) {
        return $query->where("estado_id", "14")//por facturar
                        ->orWhere("estado_id", "15"); //facturada parcial
    }

    public function scopeDeFechaCreacionMayorIgual($query, $fecha) {
        return $query->whereDate("created_at", ">=", $fecha);
    }

    public function scopeDeFechaCreacionMenorIgual($query, $fecha) {
        return $query->whereDate("created_at", "<=", $fecha);
    }

    public function scopeDeItem($query, $item_id) {
        return $query->whereHas("items", function($q) use ($item_id) {
                    $q->where("inv_items.id", $item_id);
                });
    }


    public function scopeDeProveedor($query, $proveedor_id)
    {
        $aux = is_numeric($proveedor_id) ? $proveedor_id : Proveedores_orm::where("uuid_proveedor", hex2bin($proveedor_id))->first()->id;
        return $query->where("proveedor_id", $aux);
    }

    public function scopeDeEstado($query, $estado_id) {
        return $query->where("estado_id", $estado_id);
    }

    public function scopeDeCentroContable($query, $centro_contable_id) {
        return $query->where("centro_contable_id", $centro_contable_id);
    }

    public function orden()
    {
        return $this->belongsTo("Ordenes_orm", "operacion_id", "id")
            ->where("operacion_type", "Ordenes_orm");
    }

    public function scopeDeOrdenDeCompra($query, $orden_compra_id)
    {
        return $query->whereHas("orden", function($q) use($orden_compra_id){
            $q->where("id", $orden_compra_id);
        });
    }

    public function scopeDeTipo($query, $tipo_id) {
        //18 - tipo compra
        //19 - tipo subcontrato

       // $aux = $tipo_id == "18" ? "Ordenes_orm" : "Flexio\\Modulo\\SubContratos\\Models\\SubContrato";
       // $aux = "Flexio\\Modulo\\SubContratos\\Models\\SubContrato";
        return $query->where("operacion_type", "Flexio\\Modulo\\SubContratos\\Models\\SubContrato");
    }

    public function nota_debito()
    {
        return $this->hasMany('Flexio\Modulo\NotaDebito\Models\NotaDebito','factura_id');
    }

    public function getSaldoAttribute()
    {
        $nota_debito_total = $this->nota_debito()->where('compra_nota_debitos.estado','aprobado')->sum('compra_nota_debitos.total');
        if($this->empresa->retiene_impuesto =="si" && $this->proveedor->retiene_impuesto == 'no' && $this->total > 0){
            return $this->total - $this->items_factura->sum('retenido') - $this->pagos_aplicados_suma - $nota_debito_total;
        }
        return $this->total - $this->pagos_aplicados_suma - $nota_debito_total;
    }

    public function scopeDeMontoMayorIgual($query, $monto) {
        return $query->where("total", ">=", $monto);
    }

    public function scopeDeMontoMenorIgual($query, $monto) {
        return $query->where("total", "<=", $monto);
    }

    public function scopeDePedido($query, $uuid_pedido) {
        return $query->where("uuid_pedido", hex2bin($uuid_pedido));
    }

    public function items_factura() {
        return $this->hasMany('Facturas_compras_contratos_items_orm', 'factura_id');
    }

    function empresa() {
        return $this->belongsTo('Empresa_orm', 'empresa_id');
    }

    public function refactura() {
        return $this->morphToMany(FacturaVenta::class, 'fac_facturable');
    }

}
