<?php
namespace Flexio\Modulo\NotaCredito\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\Inventarios\Models\Items;
use Flexio\Modulo\Contabilidad\Models\Impuestos;

class NotaCreditoItem extends Model{
  protected $table = 'venta_nota_credito_items';

  protected $fillable = ['cuenta_id','monto','item_id','impuesto_id','impuesto_total'];

  protected $guarded = ['id'];

  function cuenta(){
    return $this->belongsTo('Cuentas_orm','cuenta_id');
  }
  public function inventario_item(){
    return $this->belongsTo(Items::class,'item_id')->select(['id','codigo','nombre', 'descripcion']);
  }

  public function impuesto(){
    return $this->belongsTo(Impuestos::class,'impuesto_id')->select(['uuid_impuesto','impuesto','id','cuenta_id','nombre']);
  }
}
