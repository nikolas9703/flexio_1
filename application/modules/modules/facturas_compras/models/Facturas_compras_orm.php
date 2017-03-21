<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\FacturasCompras\Models\FacturaCompra;
use Flexio\Modulo\FacturasVentas\Models\FacturaVenta;
use Flexio\Modulo\Comentario\Models\Comentario as Comentario;
use Flexio\Modulo\Historial\Models\Historial;
use Flexio\Modulo\FacturasCompras\Observer\FacturaComprasObserver;
use Flexio\Notifications\Notify;

class Facturas_compras_orm extends Model
{
    use Notify;
    protected $table = 'faccom_facturas';
    protected $fillable = ['codigo', 'proveedor_id', 'empresa_id', 'fecha_hasta', 'fecha_desde', 'factura_proveedor', 'estado', 'created_by', 'comentario', 'termino_pago', 'fecha_termino_pago', 'item_precio_id', 'subtotal', 'impuestos', 'total', 'bodega_id', 'centro_contable_id', 'cotizacion_id', 'referencia', 'orden_venta_id', 'porcentaje_retencion'];
    protected $guarded = ['id', 'uuid_factura'];
    protected $appends = ['is_refactura','saldo'];
    protected $codeIgniter;

    public function __construct(array $attributes = array())
    {
        $this->codeIgniter = &get_instance();

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
        $this->codeIgniter->load->model("facturas_compras/Facturas_compras_items_orm");
        $this->codeIgniter->load->model("bodegas/Bodegas_orm");
        $this->codeIgniter->load->model("usuarios/Usuario_orm");
        $this->codeIgniter->load->model("ordenes/Ordenes_orm");
        //$this->codeIgniter->load->model("pedidos/Pedidos_orm");
    }
    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public static function boot() {
        parent::boot();
        //dd('Entro boot');
        Facturas_compras_orm::observe(FacturaComprasObserver::class);

    }

    public static function findByUuid($uuid)
    {
        return strtoupper(bin2hex($uuid));
        //return self::where('uuid_pedido', hex2bin($uuid))->first();
    }


    public function comentario()
    {
        //return $this->morphMany('Flexio\Modulo\Comentario\Models\Comentario','comentable');
        return $this->morphMany(Comentario::class, 'comentable');
    }


    public function getUuidFacturaAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function getCreatedAtAttribute($value)
    {
        return date("d-m-Y", strtotime($value));
    }

    public function getUpdatedAtAttribute($value)
    {
        return date("d-m-Y", strtotime($value));
    }

    public function getFechaDesdeAttribute($date)
    {
        return date('d-m-Y', strtotime($date));
        //echo $date;
        //return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d-m-Y');
    }

    public function getFechaHastaAttribute($date)
    {
        return '';
        //return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d-m-Y');
    }

    public function getIsRefacturaAttribute()
    {
        return $this->refactura->count() > 0;
    }

    public function getPagableAttribute()
    {
        //14 => Por pagar
        //15 => Pagada parcial
        return $this->estado_id == '14' || $this->estado_id == '15';
    }

    public function getValidaAttribute()
    {
        // 14 -> por pagar
        // 15 -> pagada parcial
        // 16 -> pagada completa
        return $this->estado_id == '14' || $this->estado_id == '15' || $this->estado_id == '16';
    }

    public function getCodigoAttribute($value)
    {
        return empty($this->factura_proveedor) ? $value : $this->factura_proveedor;
    }

    public function getCodigoEnlaceAttribute()
    {
        $attrs = [
            "class" => "link",
            "href" => base_url("facturas_compras/ver/" . $this->uuid_factura)
        ];
        $html = new Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());
        return $html->setType("htmlA")->setAttrs($attrs)->setHtml($this->codigo)->getSalida();
    }

    public function getCodigoEnlaceV2Attribute()
    {
        $attrs = [
            "class" => "link",
            "href" => base_url("facturas_compras/ver/" . $this->uuid_factura)
        ];
        $html = new Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());
        return $html->setType("htmlA")->setAttrs($attrs)->setHtml($this->codigo)->getSalida();
    }


    public function getNumeroDocumentoAttribute()
    {
        return $this->codigo;
    }

    public function getNumeroContrato()
    {
        return $this->codigo;
    }

    public function proveedor()
    {
        return $this->belongsTo('Proveedores_orm', 'proveedor_id', 'id');
    }

    public function bodega()
    {
        return $this->belongsTo('Bodegas_orm', 'bodega_id', 'id');
    }

    public function comprador()
    {
        return $this->belongsTo('Usuario_orm', 'created_by', 'id');
    }

    public function pagos()
     {
         return $this->belongsToMany('Flexio\Modulo\Pagos\Models\Pagos',"pag_pagos_pagables", "pagable_id","pago_id")
             ->where("pagable_type", 'Flexio\Modulo\FacturasCompras\Models\FacturaCompra')
             ->withPivot('monto_pagado','empresa_id')->withTimestamps();
     }

     public function pagos_aplicados()
      {
          return $this->pagos()
              ->where("pag_pagos.estado", "aplicado");
      }

    public function metodo_pago()
    {

    }

    public function operacion()
    {
        return $this->morphTo();
    }


    public function pedidos()
    {
        return $this->belongsTo('Pedidos_orm', 'uuid_pedido', 'uuid_pedido');
    }

    public function centro_contable()
    {
        return $this->belongsTo('Centros_orm', 'centro_contable_id', 'id');
    }

    public function estado()
    {
        return $this->belongsTo('Factura_catalogo_orm', 'estado_id', 'id');
    }

    public function contratos()
    {
        // return $this->where("operacion_type", "Flexio\\Modulo\\SubContratos\\Models\\SubContrato");
        return $this->belongsTo("Flexio\\Modulo\\SubContratos\\Models\\SubContrato", "operacion_id", "id")
            ->where("operacion_type", "Flexio\\Modulo\\SubContratos\\Models\\SubContrato");
    }

    public function contrato_relacionado()
    {
        // return $this->where("operacion_type", "Flexio\\Modulo\\SubContratos\\Models\\SubContrato");
        return $this->belongsTo("Flexio\\Modulo\\SubContratos\\Models\\SubContrato", "operacion_id", "id");
    }

    public function facturas_compras_items()
    {
        return $this->hasMany('Facturas_compras_items_orm', 'factura_id');
    }

    public function items()
    {
        return $this->belongsToMany('Items_orm', 'faccom_facturas_items', 'factura_id', 'item_id')
            ->withPivot("categoria_id", "cantidad", "unidad_id", "precio_unidad", "impuesto_id", "impuesto_nombre", "impuesto_cuenta", "descuento", "cuenta_id", "total", "subtotal", "descuentos", "impuestos", "cuenta_id", 'retenido');
    }

    public function items_groupByImpuestos()
    {
        return $this->items->groupBy(function ($item) {
            return $item->pivot->impuesto_id;
        });
    }

    //public function comprador() {
    public function scopeDeComprador($query, $comprador_id)
    {
        return $query->where("created_by", $comprador_id);
    }
    public function scopeDeFacturaProveedor($query, $numero_factura)
    {
        return $query->where("factura_proveedor", $numero_factura);
    }
    public function scopeDeCategoria($query, $categorias)
    {

        return $query->whereHas("items", function ($items) use ($categorias) {
            $items->whereIn("categoria_id", $categorias);
        });
    }

    public function scopeDeEmpresa($query, $empresa_id)
    {
        return $query->where("empresa_id", $empresa_id);
    }

    public function scopeDeNumeroFactura($query, $numero_factura)
    {
        foreach ($numero_factura AS $field => $value) {
            return $query->where($field, $value[0], $value[1]);
        }
    }

    public function scopeParaPagos($query)
    {
        return $query->where("estado_id", "14")//por facturar
        ->orWhere("estado_id", "15"); //facturada parcial
        // ->orWhere("estado_id",'!=', "20");//suspendida
    }

    public function scopeDeFechaCreacionMayorIgual($query, $fecha)
    {

        return $query->whereDate("fecha_desde", ">=", $fecha);
    }

    public function scopeDeFechaCreacionMenorIgual($query, $fecha)
    {
        return $query->whereDate("fecha_desde", "<=", $fecha);
    }

    public function scopeDeItem($query, $item_id)
    {
        return $query->whereHas("items", function ($q) use ($item_id) {
            $q->where("inv_items.id", $item_id);
        });
    }


    public function scopeDeProveedor($query, $proveedor_id)
    {
        if ($proveedor_id == '[object HTMLSelectElement]') {
            return $query;
        }

        $aux = is_numeric($proveedor_id) ? $proveedor_id : Proveedores_orm::where("uuid_proveedor", hex2bin($proveedor_id))->first()->id;
        return $query->where("proveedor_id", $aux);
    }

    public function scopeDeEstado($query, $estado_id)
    {
        if(is_array($estado_id))return $query->whereIn("estado_id", array_filter($estado_id));
        return $query->where("estado_id", $estado_id);
    }

    public function scopeDeCentroContable($query, $centro_contable_id)
    {

        return $query->where("centro_contable_id", $centro_contable_id);
    }

    public function orden()
    {
        return $this->belongsTo("Ordenes_orm", "operacion_id", "id")
            ->where("operacion_type", "Ordenes_orm");
    }

    /**
    * Varias ordenes relacionadas a la factura
    */
    public function ordenes() {
        return $this->morphedByMany('Flexio\Modulo\OrdenesCompra\Models\OrdenesCompra', 'facturable', 'faccom_facturables', 'factura_id', 'facturable_id');
    }

    public function subcontrato()
    {
        return $this->belongsTo('Flexio\Modulo\SubContratos\Models\SubContrato', "operacion_id", "id")
            ->where("operacion_type", "Flexio\\Modulo\\SubContratos\\Models\\SubContrato");
    }

    public function scopeDeOrdenDeCompra($query, $orden_compra_id)
    {
        return $query->whereHas("orden", function ($q) use ($orden_compra_id) {
            $q->where("ord_ordenes.id", $orden_compra_id);
        });
    }

    public function scopeDeSubcontrato($query, $subcontrato_id)
    {
        return $query->whereHas("subcontrato", function ($q) use ($subcontrato_id) {
            $q->where("sub_subcontratos.id", $subcontrato_id);
        });
    }

    public function scopeDeTipo($query, $tipo_id)
    {
        if ($tipo_id == "19"){
            return $query->where("operacion_type", "Flexio\Modulo\SubContratos\Models\SubContrato");
        }elseif ($tipo_id == "18") {
            return $query->whereRaw("operacion_type IN ('Ordenes_orm', '')");
        }
        return $query;
    }

    public function scopeDeMontoMayorIgual($query, $monto)
    {
        return $query->where("total", ">=", $monto);
    }

    public function scopeDeMontoMenorIgual($query, $monto)
    {
        return $query->where("total", "<=", $monto);
    }

    public function scopeDePedido($query, $pedido_id)
    {

        return $query->whereHas("orden", function ($orden) use ($pedido_id) {

            $orden->whereHas("pedido", function ($pedido) use ($pedido_id) {

                $pedido->where("ped_pedidos.id", $pedido_id);

            });
        });

    }

    public function items_factura()
    {
        return $this->hasMany('Facturas_compras_items_orm', 'factura_id');
    }

    public function empresa()
    {
        return $this->belongsTo('Flexio\Modulo\Empresa\Models\Empresa', 'empresa_id');
    }

    public function refactura()
    {
        return $this->morphToMany(FacturaVenta::class, 'fac_facturable');
    }


    public function getNumeroDocumentoEnlaceAttribute()
    {
        $attrs = [
            "href" => $this->enlace,
            "class" => "link"
        ];

        $html = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory);
        return $html->setType("HtmlA")->setAttrs($attrs)->setHtml($this->numero_documento)->getSalida();
    }

    public function getEnlaceAttribute()
    {
        return base_url("facturas_compras/ver/" . $this->uuid_factura);
    }

    public function nota_debito()
    {
        return $this->hasMany('Flexio\Modulo\NotaDebito\Models\NotaDebito', 'factura_id');
    }

    public function nota_debito_aprobada()
    {
        return $this->hasMany('Flexio\Modulo\NotaDebito\Models\NotaDebito', 'factura_id')->where('compra_nota_debitos.estado', 'aprobado');
    }

    public function facturas_items()
    {

        return $this->hasMany('Flexio\Modulo\FacturasCompras\Models\FacturaCompraItems', 'factura_id');

    }

    public function creditos_aplicados()
    {
        return $this->hasMany('Flexio\Modulo\CreditosAplicados\Models\CreditoAplicado', 'acreditable_id')
        ->where('cre_creditos_aplicados.acreditable_type', 'Flexio\Modulo\FacturasCompras\Models\FacturaCompra');
    }

    public function debeRetener()
    {
        if(!count($this->proveedor))throw new \Exception('No se logro determinar el proveedor del registro (Requerido)');
        return $this->empresa->retiene_impuesto == 'si' && $this->proveedor->retiene_impuesto == 'no' && $this->total > 0;
    }

    public function getTotalARetener()
    {
        if($this->debeRetener())
        {
            return round_up(round_up($this->impuestos) * 0.5);
        }
        return 0;
    }

    /*Este saldo es utilizado para calculo de del total factura dinamico
      una vez que se paga la factura el saldo va disminuyendo
    */
    public function getSaldoAttribute()
    {
        $nota_debito_total = $this->nota_debito_aprobada->sum('total') - $this->nota_debito_aprobada->sum('retenido');
        $aux = round_up($this->total)
        - $this->getTotalARetener()
        - $this->pagos_aplicados_suma
        - $nota_debito_total
        - $this->retencion// it is from subcontrato....
        - $this->creditos_aplicados->sum('total');

        return $aux < 0 ? 0 : round_up($aux);
    }

    public function getPagosAplicadosSumaAttribute()
    { //dd($this->pagos_aplicados->sum("pag_pagos_pagables.monto_pagado2"));
        return $this->pagos_aplicados()->sum("pag_pagos_pagables.monto_pagado");
    }

    function present()
    {
        return new \Flexio\Modulo\FacturasCompras\Presenter\FacturaCompraPresenter($this);
    }

    public function scopeDeFiltro($query, $campo)
    {
        $queryFilter = new \Flexio\Modulo\FacturasCompras\Services\FacturaCompraQueryFilters;
        return $queryFilter->apply($query, $campo);
    }
    public function historial(){
        return $this->morphMany(Historial::class,'historiable');
    }
    public function getModuloNotificacionesAttribute() {
        return '\Flexio\Modulo\FacturasCompras\Notifications\FacturasUpdated';
    }
    public function getModuloIdAttribute()
    {
        return 36;//table modulos(id)
    }
    public function getIconoAttribute(){
      return 'fa fa-shopping-cart';
    }
}
