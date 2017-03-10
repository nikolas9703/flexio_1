<?php
namespace Flexio\Modulo\Cotizaciones\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Devoluciones\Models\Devolucion as Devolucion;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;
use Carbon\Carbon as Carbon;


class LineItem extends Model
{
  use RevisionableTrait;

  protected $table = 'lines_items';
  protected $fillable =['tipoable_id','tipoable_type','empresa_id','item_id','cantidad','unidad_id','precio_unidad','impuesto_id','descuento','cuenta_id','','precio_total','categoria_id','impuesto_total','descuento_total','cantidad_devolucion','comentario','atributo_id','atributo_text','item_adicional','tarifa_periodo_id','tarifa_fecha_desde','tarifa_fecha_hasta','tarifa_pactada','tarifa_monto','tarifa_cantidad_periodo'];
	protected $guarded = ['id','uuid_line_item'];

  //Propiedades de Revisiones
  protected $revisionEnabled = true;
  protected $keepRevisionOf = ['item_id','cantidad','unidad_id','precio_unidad','impuesto_id','descuento','cuenta_id','','precio_total','categoria_id','impuesto_total','descuento_total','cantidad_devolucion','comentario','atributo_id','atributo_text','item_adicional','tarifa_periodo_id'];

  protected $tipo=['orden_trabajo' => 'Flexio\Modulo\OrdenesTrabajo\Models\OrdenTrabajo'];

  public function __construct(array $attributes = array()) {
    $this->setRawAttributes(array_merge($this->attributes, array(
      'uuid_line_item' => Capsule::raw("ORDER_UUID(uuid())")
    )), true);
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

  public function getUuidLineItemAttribute($value) {
    return strtoupper(bin2hex($value));
  }

  function tipoable() {
    return $this->morphTo();
  }

  public function getTipoableTypeAttribute($value){

      $tipos = array_flip($this->tipo);
      if(array_key_exists($value,$tipos)){
          return  $tipos[$value];
      }
      return $value;

  }

  public function inventario_item() {
    return $this->belongsTo('Items_orm','item_id')->select(['id','codigo','nombre', 'descripcion']);
  }

    public function atributo(){

        return $this->belongsTo('Flexio\Modulo\Atributos\Models\Atributos', 'atributo_id')
                ->where('atr_atributos.atributable_type','Items_orm');

    }

    public function getAttributes() {
        return $this->hasMany('Flexio\Modulo\Atributos\Models\Atributos','id', 'atributo_id');
    }

    public function periodo(){
        return $this->belongsTo('Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquilerCatalogos', 'tarifa_periodo_id')->select(['id','nombre']);
    }

    public function item(){

        return $this->belongsTo('Flexio\Modulo\Inventarios\Models\Items', 'item_id');

    }

    public function categoria() {
        return $this->belongsTo('Flexio\Modulo\Inventarios\Models\Categoria', 'categoria_id');
    }

    public function unidad() {
        return $this->belongsTo('Flexio\Modulo\Inventarios\Models\Unidades', 'unidad_id');
    }

  public function impuesto() {
    return $this->belongsTo('Impuestos_orm','impuesto_id')->select(['uuid_impuesto','impuesto','id','cuenta_id','nombre']);
  }

  public function cuenta() {
    return $this->belongsTo('Cuentas_orm','cuenta_id')->select(['id','uuid_cuenta']);
  }

  //Esta funcion se agreg� 22/6/2016 para poder tener comentarios por cada item
  public function comentario() {
  	return $this->hasOne(LineItemComentario::class, 'lines_items_id');
   }


   public function setCantidadAttribute($value){

        $this->attributes['cantidad'] =  str_replace(",", "", $value);
    }


    public function setPrecioTotalAttribute($value){

        $this->attributes['precio_total'] =  str_replace(",", "", $value);
    }


    

}
