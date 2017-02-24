<?php
namespace Flexio\Modulo\CotizacionesAlquiler\Models;

use Illuminate\Database\Eloquent\Model;
//use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;
use Flexio\Modulo\Atributos\Models\Atributos;
use Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquilerCatalogos;


class CotizacionesAlquilerItems extends Model
{
    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = ['contratable_id','contratable_type','categoria_id','item_id','cantidad','ciclo_id','tarifa','en_alquiler','devuelto','entregado','impuesto_id','precio_unidad','precio_total','atributo_id','atributo_text','periodo_tarifario','impuesto_total','descuento_total','cuenta_id','comentario','descuento','item_adicional','empresa_id','unidad_id'];

    protected $table    = 'contratos_items';
    protected $fillable = ['contratable_id','contratable_type','categoria_id','item_id','cantidad','ciclo_id','tarifa','en_alquiler','devuelto','entregado','impuesto_id','precio_unidad','precio_total','atributo_id','atributo_text','periodo_tarifario','impuesto_total','descuento_total','cuenta_id','comentario','descuento','item_adicional','empresa_id','unidad_id'];
    protected $guarded  = ['id'];
    protected $appends  = ['por_entregar','cuentas'];
    public $timestamps = false;

    public function __construct(array $attributes = array())
    {
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

    public function getCuentasAttribute()
    {
        return $this->item->cuentas;//computed getCuentas articulo.vue
    }

    public function getEntregadoAttribute()
    {
        if(count($this->cotizaciones_items_detalles_entregas) == 0)
        {
            return 0;
        }

        return $this->cotizaciones_items_detalles_entregas->sum(function($cotizacion_item_detalle_entrega){
            $estado = $cotizacion_item_detalle_entrega->operacion->estado_id;//4->entregado
            return ($estado == 4) ? $cotizacion_item_detalle_entrega->cantidad : 0;
        });
    }

    public function getPorEntregarAttribute()
    {
        if(count($this->cotizaciones_items_detalles_entregas) == 0)
        {
            return 0;
        }

        return $this->cotizaciones_items_detalles_entregas->sum(function($cotizacion_item_detalle_entrega){
            $estado = $cotizacion_item_detalle_entrega->operacion->estado_id;//2->por entregar
            return ($estado == 2) ? $cotizacion_item_detalle_entrega->cantidad : 0;
        });
    }

    public function getDevueltoAttribute()
    {
        return 0;//en desarrollo
    }

    public function getEnAlquilerAttribute()
    {
        return $this->entregado - $this->devuelto;
    }

    public function cotizaciones_items_detalles()
    {
        return $this->morphMany('Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquilerItemsDetalles', 'relacion');
    }

    public function cotizaciones_items_detalles_entregas()
    {
        return $this->cotizaciones_items_detalles()
                ->has('entregas');
    }

    public function item()
    {
        return $this->belongsTo('Flexio\Modulo\Inventarios\Models\Items', 'item_id')->select('id', 'nombre');
    }

    function contratable() {
      return $this->morphTo();
    }

    public function getAttributes() {
        return $this->hasMany(Atributos::class,'id', 'atributo_id');
    }

    public function categoria() {
        return $this->belongsTo('Flexio\Modulo\Inventarios\Models\Categoria', 'categoria_id');
    }

    public function periodotarifario() {
        return $this->hasOne(ContratosAlquilerCatalogos::class, 'valor', 'periodo_tarifario')->where('tipo', '=', 'tarifa');
    }

}
