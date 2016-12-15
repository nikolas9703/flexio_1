<?php
//namespace Intergracion\EntradaManual\Models;
use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Entrada_orm extends Model
{
	protected $table = 'contab_entrada_manual';
	protected $fillable = ['uuid_entrada','codigo','nombre','empresa_id'];
	protected $guarded = ['id'];

  public function __construct(array $attributes = array()){
    $this->setRawAttributes(array_merge($this->attributes, array(
      'uuid_entrada' => Capsule::raw("ORDER_UUID(uuid())")
    )), true);
    parent::__construct($attributes);
  }

  public function getCreatedAtAttribute($date){
    return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d H:i:s');
  }

  function transacciones(){
    return $this->hasMany('Transaccion_orm','transaccionable_id')->where('transaccionable_type','Entrada_orm');
  }

	function comentarios(){
    return $this->hasMany('Comentario_orm','entrada_manual_id');
  }

  public function getUuidEntradaAttribute($value){
		return strtoupper(bin2hex($value));
	}

  public static function findByUuid($uuid){
    return self::where('uuid_entrada',hex2bin($uuid))->first();
  }

  public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){
	$query = self::where($clause);
	if($sidx!=NULL && $sord!=NULL) $query->orderBy($sidx, $sord);
	if($limit!=NULL) $query->skip($start)->take($limit);
  return $query->get();
  }

	public function transaccion()
  {
    // transaccionable es la funcion de Transaccion_orm
    return $this->morphMany(Transaccion_orm::class, 'transaccionable');
  }


}
