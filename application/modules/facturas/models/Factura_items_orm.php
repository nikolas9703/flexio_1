<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Factura_items_orm extends Model
{

  protected $table = 'fac_factura_items';
	protected $fillable = ['empresa_id','factura_id','item_id','cantidad','unidad_id','precio_unidad','impuesto_id','descuento','cuenta_id','precio_total','categoria_id'];
	protected $guarded = ['id','uuid_factura_item'];

  public function __construct(array $attributes = array()){
    $this->setRawAttributes(array_merge($this->attributes, array(
      'uuid_factura_item' => Capsule::raw("ORDER_UUID(uuid())")
    )), true);
    parent::__construct($attributes);
  }

	public function toArray(){
			$array = parent::toArray();
			return $array;
	}

  public function getUuidFacturaItemAttribute($value){
    return strtoupper(bin2hex($value));
  }

    public function comp__aux()
    {
        return array(
            "uuid_item"         => $this->inventario_item->uuid_item,
            "descripcion"       => $this->inventario_item->descripcion,
            "observacion"       => "No aplica",
            "cantidad"          => $this->cantidad,
            "uuid_unidad"       => $this->unidad->uuid_unidad,
            "uuid_cuentaGasto"  => $this->inventario_item->cuentaGasto->uuid_cuenta
        );
    }

  public function factura(){
    return $this->belongsTo('Factura_orm','factura_id');
  }

  public function inventario_item(){
    return $this->belongsTo('Items_orm','item_id');
  }

  public function unidad(){
        return $this->belongsTo('Unidades_orm', 'unidad_id');
  }

  public function impuesto(){
    return $this->belongsTo('Impuestos_orm','impuesto_id')->select(['uuid_impuesto','impuesto','id']);
  }

  public function cuenta(){
    return $this->belongsTo('Cuentas_orm','cuenta_id')->select(['id','uuid_cuenta']);
  }



}
