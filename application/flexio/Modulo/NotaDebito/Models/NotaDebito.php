<?php

namespace Flexio\Modulo\NotaDebito\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Library\Util\GenerarCodigo as GenerarCodigo;
use Flexio\Modulo\FacturasCompras\Models\FacturaCompra as FacturaCompra;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;
use Flexio\Modulo\NotaDebito\Observers\NotaCreditoEventos;

class NotaDebito extends Model
{
    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = ['codigo', 'proveedor_id', 'empresa_id', 'estado', 'creado_por', 'total', 'centro_contable_id', 'entrada_manual_id', 'factura_id', 'tipo', 'fecha', 'subtotal', 'impuesto', 'retenido', 'monto_retenido'];

    protected $table = 'compra_nota_debitos';

    protected $fillable = ['codigo', 'proveedor_id', 'empresa_id', 'estado', 'creado_por', 'total', 'centro_contable_id', 'entrada_manual_id', 'factura_id', 'tipo', 'fecha', 'subtotal', 'impuesto', 'no_nota_credito', 'monto_factura', 'fecha_factura', 'retenido', 'monto_retenido'];

    protected $guarded = ['id', 'uuid_nota_debito'];
    protected $appends = ['icono', 'enlace'];

    public function __construct(array $attributes = array())
    {
        $this->setRawAttributes(array_merge($this->attributes, array('uuid_nota_debito' => Capsule::raw('ORDER_UUID(uuid())'))), true);
        parent::__construct($attributes);
    }
    /**
     * Register any other events for your application.
     */
    public static function boot()
    {
        parent::boot();

        static::observe(NotaCreditoEventos::class);
    }
    public function setCodigoAttribute($value)
    {
        return $this->attributes['codigo'] = GenerarCodigo::setCodigo('ND'.Carbon::now()->format('y'), $value);
    }
  // mutators get
  public function getFechaAttribute($date)
  {
      return Carbon::createFromFormat('Y-m-d H:i:s', $date, 'America/Panama')->format('d/m/Y');
  }

    public function getFechaNotaCreditoAttribute()
    {
        return Carbon::createFromFormat('d/m/Y', $this->fecha, 'America/Panama');
    }

    public function getNombreProveedorAttribute()
    {
        if (is_null($this->proveedor)) {
            return '';
        }

        return $this->proveedor->nombre;
    }

    public function getAProveedorAttribute()
    {
        return empty($this->factura_id) ? true : false;
    }

    public function getFechaFacturaAttribute($date)
    {
        if ($date == '0000-00-00 00:00:00') {
            return '';
        }

        return Carbon::createFromFormat('Y-m-d H:i:s', $date, 'America/Panama')->format('d/m/Y');
    }

    public function empresa()
    {
        return $this->belongsTo('Flexio\Modulo\Empresa\Models\Empresa', 'empresa_id');
    }

    public function getUuidNotaDebitoAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function getNumeroDocumentoAttribute()
    {
        return $this->factura_id;
    }

    public function getNumeroDocumentoEnlaceAttribute()
    {
        $attrs = [
    'href' => $this->enlace,
    'class' => 'link',
            ];

        $html = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());

        return $html->setType('HtmlA')->setAttrs($attrs)->setHtml($this->numero_documento)->getSalida();
    }
    public function getEnlaceAttribute()
    {
        return base_url('notas_debitos/ver/'.$this->uuid_nota_debito);
    }
//relationship
 public function centro_contable()
 {
     return $this->belongsTo('Centros_orm', 'centro_contable_id');
 }

    public function documentos()
    {
        return $this->morphMany('Flexio\Modulo\Documentos\Models\Documentos', 'documentable');
    }

    public function setFechaAttribute($date)
    {
        $this->attributes['fecha'] = Carbon::createFromFormat('d/m/Y', $date, 'America/Panama');
    }

    public function setFechaFacturaAttribute($date)
    {
        $this->attributes['fecha_factura'] = Carbon::createFromFormat('d/m/Y', $date, 'America/Panama');
    }

    public function setTotalAttribute($value)
    {
        $this->attributes['total'] = str_replace(',', '', $value);
    }

    public function setSubtotalAttribute($value)
    {
        $this->attributes['subtotal'] = str_replace(',', '', $value);
    }

    public function setImpuestoAttribute($value)
    {
        $this->attributes['impuesto'] = str_replace(',', '', $value);
    }

    public function setMontoFacturaAttribute($value)
    {
        $this->attributes['monto_factura'] = str_replace(',', '', $value);
    }

 /*public function setFechaFacturaAttribute($date){
   return $this->attributes['fecha_factura'] = Carbon::createFromFormat('m/d/Y', $date, 'America/Panama');
 }*/

 public function etapa_catalogo()
 {
     return $this->belongsTo(CatalogoNotaDebito::class, 'estado', 'etiqueta')->where('tipo', '=', 'estado');
 }

    public function items()
    {
        return $this->hasMany(NotaDebitoItem::class, 'nota_debito_id');
    }

    public function proveedor()
    {
        return $this->belongsTo('Flexio\Modulo\Proveedores\Models\Proveedores', 'proveedor_id');
    }

    public function sistema_transaccion()
    {
        return $this->morphMany('Flexio\Modulo\Transaccion\Models\SysTransaccion', 'linkable');
    }

    public function vendedor()
    {
        return $this->belongsTo('Usuario_orm', 'creado_por');
    }

    public function factura()
    {
        return $this->belongsTo(FacturaCompra::class, 'factura_id');
    }

    public function comentario()
    {
        return $this->morphMany(Comentario::class, 'comentable');
    }

 //functiones para el landing_page
 public function getIconoAttribute()
 {
     return 'fa fa-shopping-cart';
 }

    public function landing_comments()
    {
        return $this->morphMany(Comentario::class, 'comentable');
    }

    public function getModuloAttribute()
    {
        return 'Nota debito';
    }

    public function creditos_aplicados()
    {
        return $this->morphMany('Flexio\Modulo\CreditosAplicados\Models\CreditoAplicado', 'aplicable');
    }

    public function getSaldoAttribute()
    {
        return $this->total - $this->retenido - $this->creditos_aplicados->sum('total');
    }
}
