<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Comentario_retiros_orm extends Model
{
	protected $table = 'mov_retiros_comentarios';
	protected $fillable = ['comentario','retiros_id','empresa_id', 'usuario_id'];
	protected $guarded = ['id','uuid_comentario'];

  public function __construct(array $attributes = array()){

    $this->setRawAttributes(array_merge($this->attributes, array(
      'uuid_comentario' => Capsule::raw("ORDER_UUID(uuid())")
    )), true);
    parent::__construct($attributes);
  }

	function toArray(){
		$array = parent::toArray();
		$nom = $this->usuarios->toArray();
    $array['usuario'] = $nom['nombre_completo'];
    $array['fecha_creacion'] = strtotime($this->attributes['created_at']);
    $array['fecha1'] = Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['created_at'])->formatLocalized('%d de %B');
    $array['hora'] = Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['created_at'])->format('h:i a');
    $array['time_ago'] = Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['created_at'])->diffForHumans();

    return $array;
	}

  public function getCreatedAtAttribute($date){
    //return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d H:i:s');
    return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('m-d-Y H:i:s');
  }

  function recibos(){
    return $this->belongsTo('Movimiento_monetario_orm');
  }

  public function relacion(){
    return $this->morphedByMany('Usuario_orm', 'relacion');

  }

  function usuarios(){
		return $this->belongsTo('Usuario_orm','usuario_id');
  }

  function empresas(){
    return $this->belongsTo('Empresa_orm');
  }

  public function getUuidComentarioAttribute($value){
		return strtoupper(bin2hex($value));
	}

  public static function findByUuid($uuid){
    return self::where('uuid_comentario',hex2bin($uuid))->first();
  }

  public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){
		//echo $limit;
	//	echo "--".$start;
    $recibos = self::with('transacciones')->where(function($query) use($clause,$sidx, $sord, $limit,$start ){
      $query->where($clause);
      if(!empty($six))$query->orderBy($sidx, $sord);
      //if(!empty($limit)) $query->skip($start)->take($limit);
			if($limit!=NULL) $query->skip($start)->take($limit);
    })->get();
    return $recibos;
  }


}
