<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Ordenes_venta_item_orm extends Model
{

  protected $table = 'ord_ordenes_ventas_items';
	protected $fillable = ['empresa_id','orden_venta_id','item_id','cantidad','unidad_id','precio_unidad','impuesto_id','descuento','cuenta_id','precio_total','categoria_id','impuesto_total','descuento_total'];
	protected $guarded = ['id','uuid_orden_venta_item'];

  public function __construct(array $attributes = array()){
    $this->setRawAttributes(array_merge($this->attributes, array(
      'uuid_orden_venta_item' => Capsule::raw("ORDER_UUID(uuid())")
    )), true);
    parent::__construct($attributes);
  }

	public function toArray(){
			$array = parent::toArray();
			return $array;
	}

  public function getUuidOrdenVentaItemAttribute($value){
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

    public function cantidadPorFactorConversion()
    {
        //obteniendo el factor de conversion
        $factor_conversion = 1;

        foreach($this->item->item_unidades as $item_unidad)
        {
            if($item_unidad->id_unidad == $this->unidad_id)
            {
                $factor_conversion = $item_unidad->factor_conversion;
            }
        }


        return $this->cantidad * $factor_conversion;
    }

  public function orden_venta(){
    return $this->belongsTo('Orden_ventas_orm','orden_venta_id');
  }

  public function inventario_item(){
    return $this->belongsTo('Items_orm','item_id');
  }

    public function item()
    {
        return $this->belongsTo('Items_orm', 'item_id', 'id');
    }

  public function unidad(){
        return $this->belongsTo('Unidades_orm', 'unidad_id');
  }

  public function impuesto(){
    return $this->belongsTo('Impuestos_orm','impuesto_id')->select(['uuid_impuesto','impuesto']);
  }

  public function articulo(){
    return $this->belongsTo('Items_orm','item_id')->select(['uuid_item','descripcion']);
  }

  public function cuenta(){
    return $this->belongsTo('Cuentas_orm','cuenta_id')->select(['id','uuid_cuenta']);
  }



}
