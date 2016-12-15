<?php
namespace Flexio\Modulo\NotaDebito\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\Inventarios\Models\Items;
use Flexio\Modulo\Contabilidad\Models\Impuestos;

class NotaDebitoItem extends Model{
  protected $table = 'compra_nota_debito_items';

  protected $fillable = ['cuenta_id','monto','impuesto_total','impuesto_id','item_id','descripcion','precio_total'];

  protected $guarded = ['id'];

  function cuenta(){
    return $this->belongsTo('Cuentas_orm','cuenta_id');
  }
  public function inventario_item(){
    return $this->belongsTo(Items::class,'item_id')->select(['id','codigo','nombre', 'descripcion']);
  }

  public function setMontoAttribute($value){
      return  $this->attributes['monto'] = str_replace(',', '', $value);
  }

  public function setPrecioTotalAttribute($value){
      return  $this->attributes['precio_total'] = str_replace(',', '', $value);
  }

  public function impuesto(){
    return $this->belongsTo(Impuestos::class,'impuesto_id')->select(['uuid_impuesto','impuesto','id','cuenta_id','nombre']);
  }
}
