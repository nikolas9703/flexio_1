<?php
namespace Flexio\Modulo\ConfiguracionContabilidad\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable as AsientoContable;
use Flexio\Modulo\Contabilidad\Models\Cuentas;

class CuentaBanco extends Model
{
  protected $table = 'contab_cuenta_banco';
	protected $fillable = ['cuenta_id','empresa_id'];
	protected $guarded = ['id'];

  function transacciones(){
    return $this->hasMany(AsientoContable::class,'cuenta_id','cuenta_id');
  }

  function tienes_transacciones(){
    $bolean = false;
    if($this->transacciones()->count() > 0){
      $bolean = true;
    }
    return $bolean;
  }
    public function cuenta(){
	return $this->belongsTo(Cuentas::Class, 'cuenta_id', 'id');
 }
}
