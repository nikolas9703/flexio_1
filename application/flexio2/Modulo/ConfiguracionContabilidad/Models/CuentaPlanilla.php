<?php
namespace Flexio\Modulo\ConfiguracionContabilidad\Models;

use Illuminate\Database\Eloquent\Model as Model;
//models
use Flexio\Modulo\EntradaManuales\Models\AsientoContable as AsientoContable;

class CuentaPlanilla extends Model
{
    protected $table    = 'contab_planilla';
    protected $fillable = ['cuenta_id','empresa_id'];
    protected $guarded  = ['id'];

    public function transacciones()
    {
        return $this->hasMany(AsientoContable::class,'cuenta_id','cuenta_id');
    }

    public function tienes_transacciones()
    {
        return $this->transacciones()->count() > 0 ? true : false;
    }
}