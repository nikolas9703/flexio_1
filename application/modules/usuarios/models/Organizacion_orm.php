<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Organizacion_orm extends Model{

  protected $table = 'organizacion';
  protected $fillable = ['nombre','uuid_organizacion'];
  protected $guarded = ['id'];

  public function setUuidOrganizacionAttribute($value)
  {
    $this->attributes['uuid_organizacion'] = Capsule::raw("ORDER_UUID(uuid())");
  }

  public function getUuidOrganizacionAttribute($value)
  {
    return strtoupper(bin2hex($value));
  }
  public static function findByUuid($uuid){
    return self::where('uuid_organizacion',hex2bin($uuid))->first();
  }

 public function empresas()
 {
  //return $this->belongsTo('Empresa_orm');
  return $this->hasMany('Empresa_orm');
 }

 public function relacion()
 {
    return $this->morphToMany('Relacion_orm', 'relacion');
 }

 public static function listar($sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL,$orgId)
 {
   $query = self::whereIn('id',$orgId);
   //Si existen variables de orden
   if($sidx!=NULL && $sord!=NULL) $query->orderBy($sidx, $sord);
   //Si existen variables de limite
   if($limit!=NULL) $query->skip($start)->take($limit);
   return $query->get();
 }

}
