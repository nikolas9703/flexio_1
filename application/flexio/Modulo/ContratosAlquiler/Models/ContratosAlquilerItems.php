<?php
namespace Flexio\Modulo\ContratosAlquiler\Models;

use Illuminate\Database\Eloquent\Model;
//use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;
use Flexio\Models\EntregasAlquiler\Models\EntregasAlquilerItems;


class ContratosAlquilerItems extends Model
{
    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = false;
    protected $keepRevisionOf = ['contratable_id','contratable_type','categoria_id','item_id','cantidad','ciclo_id','tarifa','en_alquiler','devuelto','entregado','atributo_id','impuesto','descuento','cuenta_id'];

    protected $table    = 'contratos_items';
    protected $fillable = ['contratable_id','contratable_type','categoria_id','item_id','cantidad','ciclo_id','tarifa','en_alquiler','devuelto','entregado','atributo_id','impuesto','descuento','cuenta_id'];
    protected $guarded  = ['id'];
    protected $appends  = ['por_entregar','entregado','devuelto'];
    public $timestamps = false;


    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
    }
    public static function boot() {
        parent::boot();
    }

    public function getEntregadoAttribute()
    {

       if(count($this->contratos_items_detalles_entregas) == 0)
        {
              return 0;
        }

        return $this->contratos_items_detalles_entregas->sum(function($contrato_item_detalle_entrega){
            $estado = $contrato_item_detalle_entrega->operacion->estado_id;//4->entregado
            return ($estado == 4) ? $contrato_item_detalle_entrega->cantidad : 0;
        });
    }

    public function getPorEntregarAttribute()
    {
        if(count($this->contratos_items_detalles_entregas) == 0)
        {
            return 0;
        }

        return $this->contratos_items_detalles_entregas->sum(function($contrato_item_detalle_entrega){
            $estado = $contrato_item_detalle_entrega->operacion->estado_id;//2->por entregar
            return ($estado == 2) ? $contrato_item_detalle_entrega->cantidad : 0;
        });
    }

    public function getDevueltoAttribute()
    {

        if(count($this->contratos_items_detalles_devoluciones) == 0)
        {
              return 0;
        }
         return $this->contratos_items_detalles_devoluciones->sum(function($contrato_item_detalle_devuelto){

            $estado = $contrato_item_detalle_devuelto->operacion->estado_id;//4->entregado
            return ($estado == 2) ? $contrato_item_detalle_devuelto->cantidad : 0;
        });

    }

    public function getEnAlquilerAttribute()
    {
        return $this->entregado - $this->devuelto;
    }

    public function contratos_items_detalles()
    {
        return $this->morphMany('Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquilerItemsDetalles', 'relacion');
    }

    public function contratos_items_detalles_entregas()
    {
        return $this->contratos_items_detalles()
                ->has('entregas');
    }
    public function contratos_items_detalles_devoluciones()
    {
        return $this->contratos_items_detalles()
        ->has('devoluciones');
    }

    public function item()
    {
        return $this->belongsTo('Flexio\Modulo\Inventarios\Models\Items', 'item_id')->select('id', 'nombre', 'tipo_id');
    }

    public function ciclo()
    {
    	return $this->hasOne('Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquilerCatalogos', 'id', 'ciclo_id')->where('tipo', 'tarifa');
    }

    public function categoria() {
    	return $this->belongsTo('Flexio\Modulo\Inventarios\Models\Categoria', 'categoria_id');
    }

    public function unidad() {
    	return $this->belongsTo('Flexio\Modulo\Inventarios\Models\Unidades', 'unidad_id');
    }

    public function impuestoinfo() {
    	return $this->belongsTo('Impuestos_orm','impuesto')->select(['uuid_impuesto','impuesto','id','cuenta_id','nombre']);
    }

    public function cuenta() {
    	return $this->belongsTo('Cuentas_orm','cuenta_id')->select(['id','uuid_cuenta']);
    }
}
