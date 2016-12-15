<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Item_cuentas_orm extends Model
{
	protected $table = 'contab_cuentas';
	protected $fillable = ['codigo','nombre','detalle','padre_id','tipo_cuenta_id','empresa_id'];
	protected $guarded = ['id'];

  function tipo_cuentas(){
    return $this->belongsTo('Tipo_cuentas_orm');
  }

  public function cuentas()
  {
        return $this->belongsToMany('Cuentas_orm', 'contab_cuentas', 'id');
  }

  

}
