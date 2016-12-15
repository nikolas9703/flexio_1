<?php
namespace Flexio\Modulo\Contabilidad\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;

class Cuentas extends Model
{

    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = ['codigo','nombre','detalle','estado','balance','padre_id', 'tipo_cuenta_id', 'empresa_id', 'impuesto_id', 'uuid_cuenta'];

    use SignoCuenta;
    protected $table        = 'contab_cuentas';
    protected $fillable     = ['codigo','nombre','detalle','estado','balance','padre_id', 'tipo_cuenta_id', 'empresa_id', 'impuesto_id', 'uuid_cuenta'];
    protected $guarded      = ['id'];
    public $timestamps      = true;
    protected $appends = ['is_padre'];


    //mutators
    public function getUuidCuentaAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function getIsPadreAttribute(){
        return $this->cuentas_item->count() > 0;
    }

    public function getNombreCompletoAttribute()
    {
        return $this->codigo.' '.$this->nombre;
    }

    public function getBalanceDebitoAttribute()
    {
        return in_array($this->tipo_cuenta_id, [1,5]);
    }

    public function getBalanceCreditoAttribute()
    {
        return in_array($this->tipo_cuenta_id, [2,3,4]);
    }

    //relationships
    function tipo_cuentas(){
      return $this->belongsTo(TipoCuenta::class,'tipo_cuenta_id');
    }

    public function cuentas(){
       return $this->belongsTo(Cuentas::class, 'padre_id');
    }

    public function cuentas_item(){
      return $this->hasMany(Cuentas::class,'padre_id','id');
    }

    public function transacciones(){
      return $this->hasMany(AsientoContable::class,'cuenta_id');
    }

    //scopes
    public function scopeDeEmpresa($query, $empresa_id)
    {
        return $query->where("empresa_id", $empresa_id);
    }

    public function scopeDeTipo($query, $tipo_cuenta_id)
    {
        if(is_array($tipo_cuenta_id)){
            return $query->whereIn("tipo_cuenta_id", $tipo_cuenta_id);
        }
        return $query->where("tipo_cuenta_id", $tipo_cuenta_id);
    }

    public function scopeDePadre($query, $padre_id)
    {
        return $query->where("padre_id", $padre_id);
    }

    function scopeTransaccionalesDeEmpresa($query, $empresa_id)
    {
        return  $query->where("empresa_id", $empresa_id)
                ->whereNotIn("id", function($q) use ($empresa_id){
                    $q->select("padre_id")
                    ->from("contab_cuentas")
                    ->where("empresa_id", $empresa_id);
                });
    }

    function signoCuenta($transaccion){
        return $this->signo($transaccion);
    }
}
