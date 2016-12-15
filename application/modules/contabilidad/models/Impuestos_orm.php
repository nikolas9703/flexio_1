<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Impuestos_orm extends Model
{
  protected $table = 'contab_impuestos';
	protected $fillable = ['nombre','descripcion','impuesto','estado','empresa_id','cuenta_id'];
	protected $guarded = ['id','uuid_impuesto'];

  public function __construct(array $attributes = array())
   {
    $this->setRawAttributes(array_merge($this->attributes, array(
      'uuid_impuesto' => Capsule::raw("ORDER_UUID(uuid())")
    )), true);
    parent::__construct($attributes);
  }



  function cuenta(){
    return $this->belongsTo('Cuentas_orm');
  }

  function empresas(){
    return $this->belongsTo('Empresa_orm');
  }


  public function getUuidImpuestoAttribute($value)
  {
    return strtoupper(bin2hex($value));
  }

  public static function findByUuid($uuid){
    return self::where('uuid_impuesto',hex2bin($uuid))->first();
  }

  public static function listar_grid($clause = array()){
    $impuestos = self::where($clause)->get();
    return $impuestos;
  }

  public static function impuesto_select($clause = array()){
    $impuestos = self::where($clause)->get();
    if(empty($impuestos->toArray())) return array();
    $item = array();
    foreach($impuestos->toArray() as $impuesto ){
      array_push($item, array('id'=> $impuesto['id'],'value' => $impuesto['nombre'] ." ".$impuesto['impuesto']));
    }
    return $item;
  }


}
