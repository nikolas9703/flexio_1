<?php
namespace Flexio\Modulo\ConfiguracionContabilidad\Models;

use Illuminate\Database\Eloquent\Model as Model;

use Flexio\Modulo\EntradaManuales\Models\AsientoContable as AsientoContable;

class CajaMenuda extends Model
{
    protected $table    = 'contab_config_caja_menuda';
    protected $fillable = ['cuenta_id','empresa_id'];
    protected $guarded  = ['id'];

    public function transacciones()
    {
        return $this->hasMany(AsientoContable::class,'cuenta_id','cuenta_id');
    }
    public function cuenta()
    {
    	return $this->belongsTo('\Flexio\Modulo\Contabilidad\Models\Cuentas', 'cuenta_id');
    }
    public function tienes_transacciones()
    {
        return $this->transacciones()->count() > 0 ? true : false;
    }
}
