<?php
namespace Flexio\Modulo\Contabilidad\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable;
use Flexio\Modulo\Contabilidad\Models\CuentasCentro;

use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;

class Cuentas extends Model
{

    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = ['codigo','nombre','detalle','estado','balance','padre_id', 'tipo_cuenta_id', 'empresa_id', 'usuario_id', 'uuid_cuenta'];

    use SignoCuenta;
    protected $table = 'contab_cuentas';
    protected $fillable = ['codigo','nombre','detalle','estado','balance','padre_id', 'tipo_cuenta_id', 'empresa_id', 'usuario_id', 'uuid_cuenta'];
    protected $guarded = ['id'];
    public $timestamps = true;

    public function __construct(array $attributes = array())
       {
           $this->setRawAttributes(array_merge($this->attributes, array(
              'uuid_cuenta' => Capsule::raw("ORDER_UUID(uuid())")
           )), true);
           parent::__construct($attributes);
       }

    //mutators

    public function cuentas_centros($centro_id)
    {
       return $this->hasMany(CuentasCentro::Class, 'cuenta_id', 'id')->where('centro_id', $centro_id)->get();
   }

   public function centros()
   {
       return $this->hasMany(CuentasCentro::Class, 'cuenta_id', 'id');
   }

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

    public function scopeDeNombre($query, $nombre)
    {
         return $query->where("nombre", "like", "%$nombre%");
    }
    public function scopeEstadoCuenta($query, $clause)
    {
        if ($clause['estado'] == 'Habilitar'){
            return $query->select('contab_cuentas.*', 'contab_cuentas_centros.cuenta_id')
                ->leftJoin('contab_cuentas_centros', function ($join) use ($clause){
                    $join->on('contab_cuentas_centros.cuenta_id', '=', 'contab_cuentas.id');
                    $join->where('contab_cuentas_centros.centro_id', '=',$clause['centro_id']);})
                ->whereNotNull('contab_cuentas_centros.cuenta_id');
        }else{
            return $query->select('contab_cuentas.*', 'contab_cuentas_centros.cuenta_id')
                ->leftJoin('contab_cuentas_centros', function ($join) use ($clause){
                    $join->on('contab_cuentas_centros.cuenta_id', '=', 'contab_cuentas.id');
                    $join->where('contab_cuentas_centros.centro_id', '=',$clause['centro_id']);})
                ->whereNull('contab_cuentas_centros.cuenta_id');
        }
    }
    public function scopeDeEmpresa($query, $empresa_id)
    {

        return $query->where("contab_cuentas.empresa_id", $empresa_id);
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
    public function scopeDeCodigo($query, $codigo)
    {
         return $query->where("codigo", "like", "%$codigo%");
    }
    //lista solo las cuentas activas
    function scopeActivas($query)
    {
        return $query->where("estado", "1");
    }

    function scopeTransaccionalesDeEmpresa($query, $empresa_id)
    {
        return  $query->where("contab_cuentas.empresa_id", $empresa_id)
                ->whereNotIn("id", function($q) use ($empresa_id){
                    $q->select("padre_id")
                    ->from("contab_cuentas")
                    ->where("empresa_id", $empresa_id);
                });
    }

    function signoCuenta($transaccion){
        return $this->signo($transaccion);
    }

    public function scopeDeFiltro($query, $campo)
    {
        $queryFilter = new \Flexio\Modulo\Contabilidad\Services\HistorialCuentaQueryFilters;
        return $queryFilter->apply($query, $campo);
    }
}
