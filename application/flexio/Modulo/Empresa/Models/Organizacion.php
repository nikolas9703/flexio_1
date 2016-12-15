<?php
namespace Flexio\Modulo\Empresa\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Organizacion extends Model{

  protected $table = 'organizacion';
  protected $fillable = ['nombre'];
  protected $guarded = ['id'];

  public function __construct(array $attributes = array()) {
        $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_organizacion' => Capsule::raw("ORDER_UUID(uuid())")
                )), true);
        parent::__construct($attributes);
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
  return $this->hasMany('Empresa_orm');
 }

 public function relacion()
 {
    return $this->morphToMany('Relacion_orm', 'relacion');
 }

}
