<?php

namespace Flexio\Modulo\FacturasCompras\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\FacturasVentas\Models\FacturaVenta;
use Flexio\Modulo\NotaDebito\Models\NotaDebito as NotaDebito;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\Documentos\Models\Documentos;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;
use Flexio\Politicas\PoliticableTrait;
use Flexio\Modulo\Historial\Models\Historial;
use Flexio\Modulo\FacturasCompras\Observer\FacturaComprasObserver;
use Flexio\Modulo\FacturasCompras\Observer\FacturaCompraEstadoObserver;

class FacturaCompra extends Model
{
    use PoliticableTrait;
    use RevisionableTrait;

    // propiedad de politica
    protected $politica = 'factura_compra';
    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = ['codigo', 'proveedor_id', 'empresa_id', 'fecha_hasta', 'fecha_desde', 'factura_proveedor', 'estado_id', 'created_by', 'comentario', 'termino_pago', 'fecha_termino_pago', 'subtotal', 'impuestos', 'total', 'bodega_id', 'centro_contable_id', 'referencia'];

    protected $table = 'faccom_facturas';

    protected $fillable = ['codigo', 'proveedor_id', 'empresa_id', 'fecha_hasta', 'fecha_desde', 'factura_proveedor', 'estado_id', 'created_by', 'comentario', 'termino_pago', 'fecha_termino_pago', 'subtotal', 'impuestos', 'retencion', 'total', 'bodega_id', 'centro_contable_id', 'referencia', 'porcentaje_retencion'];

    protected $guarded = ['id', 'uuid_factura'];

    protected $appends = ['is_refactura', 'icono', 'enlace', 'monto', 'retenido'];

    public function __construct(array $attributes = array())
    {
        $this->setRawAttributes(array_merge($this->attributes, array('uuid_factura' => Capsule::raw('ORDER_UUID(uuid())'))), true);
        parent::__construct($attributes);
    }
    /**
     * Register any other events for your application.
     */
    public static function boot()
    {
        parent::boot();
        static::observe(FacturaComprasObserver::class);
        static::observe(FacturaCompraEstadoObserver::class);
    }
    //Mutators
    public function getUuidFacturaAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function getUuidAttribute()
    {
        return $this->uuid_factura;
    }

    public function comentario()
    {
        return $this->morphMany(Comentario::class, 'comentable');
    }

    public function getCodigoAttribute($value)
    {
        return empty($this->factura_proveedor) ? $value : $this->factura_proveedor;
    }

    public function getNumeroDocumentoAttribute()
    {
        return $this->codigo;
    }
    public function getNumeroDocumentoEnlaceAttribute()
    {
        return '<a href="'.base_url('facturas_compras/ver/'.$this->uuid_factura).'" class="link">'.$this->numero_documento.'</a>';
    }

    //se usa en documentos main columna relacionado
    public function getRelacionadoAAttribute()
    {
        return $this->numero_documento_enlace.' - '.$this->proveedor->nombre;
    }

    public function getFechaDesdeAttribute($date)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d/m/Y');
    }

    public function getFechaHastaAttribute($date)
    {
        return '';
    }

    public function getEnlaceAttribute()
    {
        return base_url('facturas_compras/ver/'.$this->uuid_factura);
    }

    public function getCodigoEnlaceV2Attribute()
    {
        $attrs = [
           'class' => 'link',
           'href' => $this->enlace,
       ];
        $html = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());

        return $html->setType('htmlA')->setAttrs($attrs)->setHtml($this->codigo)->getSalida();
    }

    public function getValidaAttribute()
    {
        // 14 -> por pagar
        // 15 -> pagada parcial
        // 16 -> pagada completa
        return $this->estado_id == '14' || $this->estado_id == '15' || $this->estado_id == '16';
    }

    public function getCreatedAtAttribute($date)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d-m-Y');
    }
    public function setFechaDesdeAttribute($date)
    {
        return  $this->attributes['fecha_desde'] = Carbon::createFromFormat('d-m-Y', $date, 'America/Panama');
    }

    public function setFechaHastaAttribute($date)
    {
        return $this->attributes['fecha_hasta'] = Carbon::createFromFormat('d-m-Y', $date, 'America/Panama');
    }

    public function getIsRefacturaAttribute()
    {
        return $this->refactura->count() > 0;
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

    //muestra siempre el saldo real de la factura
    public function getMontoAttribute()
    {
        if (!empty($this->empresa) && $this->empresa->retiene_impuesto == 'si' && !empty($this->proveedor) && $this->proveedor->retiene_impuesto == 'no' && $this->total > 0) {
            return  round($this->total - $this->impuesto * 0.5 - $this->retencion, 2, PHP_ROUND_HALF_UP);
        }

        return round($this->total - $this->retencion, 2, PHP_ROUND_HALF_UP);
    }

    public function getRetenidoAttribute()
    {
        if (!empty($this->empresa) && $this->empresa->retiene_impuesto == 'si' && !empty($this->proveedor) && $this->proveedor->retiene_impuesto == 'no' && $this->total > 0) {
            return $this->facturas_items->sum('retenido');
        }

        return 0;
    }

    public function getPagosAplicadosSumaAttribute()
    {
        return $this->pagos_aplicados()->sum('pag_pagos_pagables.monto_pagado');
    }

    public function getPagosTodosSumaAttribute()
    {
        //anulados is not include
        return $this->pagos_todos()->sum('pag_pagos_pagables.monto_pagado');
    }

    public function getPagosRetenidosTodosSumaAttribute()
    {
        //anulados is not include
        return $this->pagos_de_retenido_todos()->sum('pag_pagos_pagables.monto_pagado');
    }

    //Relationships
    public function operacion()
    {
        return $this->morphTo();
    }

    public function pagos()
    {
        return $this->morphToMany('Flexio\Modulo\Pagos\Models\Pagos', 'pagable', 'pag_pagos_pagables', '', 'pago_id')
        ->withPivot('monto_pagado', 'empresa_id')->withTimestamps();
    }

    ///utilizado en notas de debitos
    public function facturas_compras_items()
    {
        return $this->hasMany('Facturas_compras_items_orm', 'factura_id');
    }

    public function facturas_items()
    {
        return $this->hasMany('Flexio\Modulo\FacturasCompras\Models\FacturaCompraItems', 'factura_id');
    }

    public function pagos_de_retenido_aplicados()
    {
        return $this->pagos()
        ->where('pag_pagos.formulario', 'retenido')
        ->where('pag_pagos.estado', 'aplicado');
    }

    public function getRetenidoPagadoAttribute()
    {
        //dd($this->pagos_de_retenido_aplicados->toArray());
        return $this->pagos_de_retenido_aplicados->sum('pivot.monto_pagado');
    }

    public function getRetenidoPorPagarAttribute()
    {
        return $this->retencion - $this->retenido_pagado;
    }

    public function pagos_de_retenido_todos()
    {
        return $this->pagos()
        ->where('pag_pagos.formulario', 'retenido')
        ->where('pag_pagos.estado', '!=', 'anulado');
    }

    public function pagos_aplicados()
    {
        return $this->pagos()
            ->where('pag_pagos.estado', 'aplicado');
    }

    public function pagos_todos()
    {
        return $this->pagos()
            ->where('pag_pagos.estado', '!=', 'anulado');
    }

    public function bodega()
    {
        return $this->belongsTo('Bodegas_orm', 'bodega_id', 'id');
    }

    /**
     * Varias ordenes relacionadas a la factura.
     */
    public function ordenes()
    {
        return $this->morphedByMany('Flexio\Modulo\OrdenesCompra\Models\OrdenesCompra', 'facturable', 'faccom_facturables', 'factura_id', 'facturable_id');
    }

    public function comp_numeroDocumento()
    {
        return $this->codigo;
    }

    public function items()
    {
        return $this->belongsToMany('Items_orm', 'faccom_facturas_items', 'factura_id', 'item_id')
                        ->withPivot('categoria_id', 'cantidad', 'unidad_id', 'precio_unidad', 'impuesto_id', 'impuesto_nombre', 'impuesto_cuenta', 'descuento', 'cuenta_id', 'total', 'subtotal', 'descuentos', 'impuestos', 'cuenta_id');
    }

    public function items_groupByImpuestos()
    {
        return $this->items->groupBy(function ($item) {
            return $item->pivot->impuesto_id;
        });
    }

    public function refactura()
    {
        return $this->morphToMany(FacturaVenta::class, 'fac_facturable');
    }

    public function items2()
    {
        return $this->belongsToMany("Flexio\Modulo\Inventarios\Models\Items", 'faccom_facturas_items', 'factura_id', 'item_id')
            ->withPivot('item_id', 'unidad_id', 'precio_unidad', 'cantidad', 'total');
    }

    public function vendedor()
    {
        return $this->belongsTo('Usuario_orm', 'created_by');
    }
    public function proveedor()
    {
        return $this->belongsTo('Flexio\Modulo\Proveedores\Models\Proveedores', 'proveedor_id', 'id');
    }

    public function centro_contable()
    {
        return $this->belongsTo('Flexio\Modulo\CentrosContables\Models\CentrosContables', 'centro_contable_id', 'id');
    }

    public function estado()
    {
        return $this->belongsTo('Flexio\Modulo\FacturasVentas\Models\FacturaVentaCatalogo', 'estado_id', 'id')
                ->where('tipo', 'estado_factura_compra');
    }
    public function etapa_catalogo()
    {
        return $this->belongsTo(FacturaVentaCatalogo::class, 'estado', 'etiqueta')->where('tipo', '=', 'etapa');
    }

    public function scopePagadoCompleto($query)
    {
        return $this->where('estado_id', '=', '16');
    }
     //Esta funcion se creó por un card del soporte, que dice que debe aparecer las facturas no solo completo, si no del tipo 14 15 16 al crear debito y credito

    public function scopeEstadosValidos($query)
    {
        return $this->whereIn('faccom_facturas.estado_id', array(14, 15, 16));
    }
    public function scopeEstadosReporte($query)
    {
        return $query->whereHas('estado', function ($query) {
            $query->whereIn('etiqueta', ['por_pagar', 'pagada_parcial', 'pagada_completa', 'suspendida']);
        });
    }

    public function empresa()
    {
        return $this->belongsTo('Flexio\Modulo\Empresa\Models\Empresa', 'empresa_id');
    }

    public function scopeDeEmpresa($query, $empresa_id)
    {
        return $query->where('empresa_id', $empresa_id);
    }

    public function scopeDeItem($query, $item_id)
    {
        return $query->whereHas('facturas_items', function ($q) use ($item_id) {
            $q->where('faccom_facturas_items.item_id', $item_id);
        });
    }

    public function scopeDeUuids($query, $uuids)
    {
        $aux = [];

        foreach ($uuids as $uuid) {
            $aux[] = hex2bin($uuid);
        }

        return $query->whereIn('uuid_factura', $aux);
    }

    public function nota_debito()
    {
        return $this->hasMany(NotaDebito::class, 'factura_id');
    }

    public function nota_debito_aprobada()
    {
        return $this->hasMany(NotaDebito::class, 'factura_id')->where('compra_nota_debitos.estado', 'aprobado');
    }

    public function contrato()
    {
        return $this->where('operacion_type', 'Flexio\\Modulo\\SubContratos\\Models\\SubContrato');
       //return $this->where("operacion_type", "Flexio\\Modulo\\SubContratos\\Models\\SubContrato");
    }

    public function documentos()
    {
        return $this->morphMany(Documentos::class, 'documentable');
    }
    //funciones del landing_page
    public function getIconoAttribute()
    {
        return 'fa fa-shopping-cart';
    }
    public function landing_comments()
    {
        return $this->morphMany(Comentario::class, 'comentable');
    }
    public function historial()
    {
        return $this->morphMany(Historial::class, 'historiable');
    }

    public function creditos_aplicados()
    {
        return $this->morphMany('Flexio\Modulo\CreditosAplicados\Models\CreditoAplicado', 'acreditable');
    }

    public function contrato_relacionado()
    {
        return $this->belongsTo("Flexio\Modulo\SubContratos\Models\SubContrato", 'operacion_id', 'id')
       ->select('sub_subcontratos.*')
       ->join('faccom_facturas', function ($join) {
           $join->on('faccom_facturas.operacion_id', '=', 'sub_subcontratos.id');
       })->where('faccom_facturas.operacion_type', '=', 'Flexio\Modulo\SubContratos\Models\SubContrato');
    }

    public function orden_compra()
    {
        return $this->belongsTo("Flexio\Modulo\OrdenesCompra\Models\OrdenesCompra", 'operacion_id', 'id')
       ->select('ord_ordenes.*')
       ->join('faccom_facturas', function ($join) {
           $join->on('faccom_facturas.operacion_id', '=', 'ord_ordenes.id');
       })->where('faccom_facturas.operacion_type', '=', 'Ordenes_orm');
    }

    //card 8 flexio board 2017
    public function getCreditoFavorAttribute()
    {
        $aplied_credit = 0;
        $this->proveedor->facturasContable->each(function ($factura) use (&$aplied_credit) {
            $aplied_credit += $factura->creditos_aplicados->sum('total');
        });
        $applicable_credit = count($this->proveedor->anticipos_aprobados) ? $this->proveedor->anticipos_aprobados->sum('monto') : 0;
        $applicable_credit += $this->proveedor->credito;
        if (count($this->contrato_relacionado)) {
            $applicable_credit += count($this->contrato_relacionado->anticipos_aprobados) ? $this->contrato_relacionado->anticipos_aprobados->sum('monto') : 0;

            return $applicable_credit - $aplied_credit;
        } elseif (count($this->orden_compra)) {
            $applicable_credit += count($this->orden_compra->anticipos_aprobados) ? $this->orden_compra->anticipos_aprobados->sum('monto') : 0;

            return $applicable_credit - $aplied_credit;
        }

        return $applicable_credit - $aplied_credit;
    }

    public function present()
    {
        return new \Flexio\Modulo\FacturasCompras\Presenter\FacturaCompraPresenter($this);
    }

    public function collection()
    {
        return new \Flexio\Modulo\FacturasCompras\Collection\FacturaCompraCollection($this);
    }

    public function getModuloAttribute()
    {
        return 'Factura compra';
    }
    public function getModuloNotificacionesAttribute()
    {
        return '\Flexio\Modulo\FacturasCompras\Notifications\FacturasUpdated';
    }

    public function scopeDeFiltro($query, $campo)
    {
        $queryFilter = new \Flexio\Modulo\FacturasCompras\Services\FacturaCompraQueryFilters();

        return $queryFilter->apply($query, $campo);
    }
}
