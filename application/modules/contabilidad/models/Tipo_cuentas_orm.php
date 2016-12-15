<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Tipo_cuentas_orm extends Model
{
	protected $table = 'contab_tipo_cuentas';
	protected $fillable = ['uuid_contabilidad','codigo','nombre'];
	protected $guarded = ['id'];

	public function setUuidContabilidadAttribute($value)
  {
    $this->attributes['uuid_empresa'] = Capsule::raw("ORDER_UUID(uuid())");
  }

	public function getUuidContabilidadAttribute($value)
  {
    return strtoupper(bin2hex($value));
  }

  public static function findByUuid($uuid){
    return Tipo_cuentas_orm::where('uuid_contabilidad',hex2bin($uuid))->first();
  }

	public function cuentas(){
		return $this->hasMany('Cuentas_orm', 'tipo_cuenta_id');
	}

}
