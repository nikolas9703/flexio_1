<?php
namespace Flexio\Modulo\Contabilidad\Models;

use Illuminate\Database\Eloquent\Model as Model;
// usar el modelo de contabilidad/ impuesto

class Impuestos extends Model
{
    protected $table        = 'contab_impuestos';
    protected $fillable     = ['uuid_impuesto','nombre','descripcion','impuesto','estado','empresa_id'];
    protected $guarded      = ['id'];
    public $timestamps      = true;

    public function getUuidImpuestoAttribute($value) {
        return strtoupper(bin2hex($value));
    }

    public function cuenta() {
        return $this->belongsTo('Flexio\Modulo\Contabilidad\Models\Cuentas', 'cuenta_id');
    }
}
