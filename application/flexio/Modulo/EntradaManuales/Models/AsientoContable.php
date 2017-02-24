<?php
namespace Flexio\Modulo\EntradaManuales\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\Transaccion\Models\SysTransaccion;
use Flexio\Modulo\Contabilidad\Models\Cuentas;

class AsientoContable extends Model
{
	protected $table = 'contab_transacciones';
	protected $primaryKey = 'id';
	protected $fillable = ['codigo','nombre','debito','credito','empresa_id','cuenta_id',
	                      'centro_id','conciliacion_id','colaborador_id','balance_verificado','order','created_at'];
	protected $guarded = ['id','uuid_transaccion'];

	public function __construct(array $attributes = array()){
	    $this->setRawAttributes(array_merge($this->attributes, array(
	      'uuid_transaccion' => Capsule::raw("ORDER_UUID(uuid())")
	    )), true);
	    parent::__construct($attributes);
	}


	public function transaccionable()
    {
       return $this->morphTo();
    }

    public function getUuidTransaccionAttribute($value){
		return strtoupper(bin2hex($value));
	}

	public function setDebitoAttribute($value)
	{
        return $this->attributes['debito'] = str_replace(',', '', $value);
    }
	public function setCreditoAttribute($value)
	{
        return $this->attributes['credito'] = str_replace(',', '', $value);
    }

	public function getNombreCentroContableAttribute(){
		if(is_null($this->centro_contable)){
			return "N/D";
		}
		return $this->centro_contable->nombre;
	}

	public function getCuentaContableAttribute(){
		if(is_null($this->cuentas)){
			return "";
		}
		return $this->cuentas->codigo. " - ". $this->cuentas->nombre;
	}


    public function getMontoAttribute()
    {
        return $this->debito + $this->credito;
    }

    public function getColorAttribute()
    {
        return empty((float)$this->debito) ? 'red' : 'green';
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('d/m/Y');
    }

    public function scopeNoConciliados($query)
    {
        return $query->where("conciliacion_id", '<', '1');
    }

    public function scopeDeEmpresa($query, $empresa_id)
    {
        return $query->where("empresa_id", $empresa_id);
    }

    public function scopeDeCuenta($query, $cuenta_id)
    {
        return $query->where("cuenta_id", $cuenta_id);
    }
		public function scopeDeColaborador($query, $colaborador_id)
		{
				return $query->where("colaborador_id", $colaborador_id);
		}
    public function scopeDeFechaInicio($query, $fecha_inicio)
    {
        $aux = Carbon::createFromFormat('d/m/Y', $fecha_inicio)->startOfDay();
        return $query->whereDate("created_at", '>=', $aux);
    }

    public function scopeDeFechaFin($query, $fecha_fin)
    {
        $aux = Carbon::createFromFormat('d/m/Y', $fecha_fin)->endOfDay();
        return $query->where("created_at", '<=', $aux);
    }

    public static function findByUuid($uuid){
        return self::where('uuid_transaccion',hex2bin($uuid))->first();
    }

	/*public function transaccionable()
    {
        return $this->morphedByMany('SysTransaccion','transaccion','contab_transacciones','id');
    }*/

	public function cuentas(){
		return $this->belongsTo(Cuentas::class,'cuenta_id');
	}

	public function centro_contable()
    {
        return $this->belongsTo('Flexio\Modulo\CentrosContables\Models\CentrosContables', 'centro_id', 'id');
    }

	public function present(){
		return new \Flexio\Modulo\EntradaManuales\Presenter\TransaccionPresenter($this);
	}

	public function scopeFiltro($query, $campo){
        $queryFilter = new \Flexio\Modulo\Contabilidad\Services\HistorialCuentaQueryFilters;
        return $queryFilter->apply($query, $campo);
    }

}
