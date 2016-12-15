<?php
namespace Flexio\Modulo\DepreciacionActivosFijos\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\Inventarios\Models\Items as Items;


class DepreciacionActivoFijoItem extends Model{

  protected $table = 'dep_depreciaciones_activos_fijos_items';
	protected $fillable = ['item_id','empresa_id','porcentaje','depreciacion_id','valor_inicial','valor_actual','monto_depreciado','codigo_serial','serial_id'];
	protected $guarded = ['id'];

  function depreciacion() {
    return $this->belongsTo(DepreciacionActivoFijo::class,'depreciacion_id');
  }

  public function items_activo_fijo(){
    return $this->belongsto(Items::class,'item_id')->select(['nombre','codigo','descripcion', 'id']);
  }
    public function item() {
        return $this->belongsto('Flexio\Modulo\Inventarios\Models\Items','item_id');
    }

    public function scopeDeEmpresa($query, $empresa_id)
    {
        return $query->where("empresa_id", $empresa_id);
    }

    public function scopeDeCodigoSerial($query, $codigo_serial)
    {
        return $query->where("codigo_serial", $codigo_serial);
    }

    public function scopeDeItem($query, $item_id)
    {
        return $query->where("item_id", $item_id);
    }

}
