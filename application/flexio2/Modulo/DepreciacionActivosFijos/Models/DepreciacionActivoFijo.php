<?php
namespace Flexio\Modulo\DepreciacionActivosFijos\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Library\Util\GenerarCodigo as GenerarCodigo;
use Flexio\Modulo\Inventarios\Models\Categoria as Categoria;

class DepreciacionActivoFijo extends Model{

    protected $table = 'dep_depreciaciones_activos_fijos';
	protected $fillable = ['codigo','categoria_id','empresa_id','referencia','centro_contable_id','porcentaje'];
	protected $guarded = ['id','uuid_depreciacion'];

  public function __construct(array $attributes = array()){
    $this->setRawAttributes(array_merge($this->attributes, array(
      'uuid_depreciacion' => Capsule::raw("ORDER_UUID(uuid())")
    )), true);
    parent::__construct($attributes);
  }

  //mutators set

  function setCodigoAttribute($value){
      return $this->attributes['codigo'] = GenerarCodigo::setCodigo('DAF'.Carbon::now()->format('y'), $value);
  }

  // mutators get
 public function getCreatedAtAttribute($date){
    return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('m/d/Y');
 }

 public function getUuidDepreciacionAttribute($value){
    return strtoupper(bin2hex($value));
  }


 //relationships

 public function centro_contable(){
   return $this->belongsTo('Centros_orm','centro_contable_id');
 }
 
 public function items(){
   return $this->hasMany(DepreciacionActivoFijoItem::class,'depreciacion_id');
 }

 public function total(){
   return $this->items->sum('monto_depreciado');
 }

 public function categoria_item(){
   return $this->belongsTo(Categoria::class, 'categoria_id');
  }



}
