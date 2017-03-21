<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Cotizacion_item_orm extends Model
{

  protected $table = 'cotz_cotizaciones_items';
	protected $fillable = ['codigo','cotizacion_id','empresa_id','item_id','cantidad','unidad_id','precio_unidad','impuesto_id','descuento','cuenta_id','','precio_total','categoria_id','total_impuesto','total_descuento'];
	protected $guarded = ['id','uuid_cotizacion_item'];

  public function __construct(array $attributes = array()){
    $this->setRawAttributes(array_merge($this->attributes, array(
      'uuid_cotizacion_item' => Capsule::raw("ORDER_UUID(uuid())")
    )), true);
    parent::__construct($attributes);
  }

	public function toArray(){
			$array = parent::toArray();
			return $array;
	}

  public function getUuidCotizacionItemAttribute($value){
    return strtoupper(bin2hex($value));
  }

  public function cotizacion(){
    return $this->belongsTo('Cotizacion_orm','cotizacion_id');
  }

  public function inventario_item(){
    return $this->belongsTo('Items_orm','item_id');
  }

  public function impuesto(){
    return $this->belongsTo('Impuestos_orm','impuesto_id')->select(['uuid_impuesto','impuesto']);
  }

  public function cuenta(){
    return $this->belongsTo('Cuentas_orm','cuenta_id')->select(['id','uuid_cuenta']);
  }




}
