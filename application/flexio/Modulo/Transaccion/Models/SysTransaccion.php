<?php
namespace Flexio\Modulo\Transaccion\Models;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable;
use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class SysTransaccion extends Model
{
    protected $table = 'sys_transacciones';
	protected $fillable = ['codigo','nombre','empresa_id','linkable_id','linkable_type'];
	protected $guarded = ['id','uuid_transaccion'];

    public function __construct(array $attributes = array())
    {
        $this->setRawAttributes(array_merge($this->attributes, array(
          'uuid_transaccion' => Capsule::raw("ORDER_UUID(uuid())")
        )), true);
        parent::__construct($attributes);
    }

    function transacciones()
    {
        return $this->hasMany(AsientoContable::class,'transaccionable_id')
                    ->where('transaccionable_type',SysTransaccion::class);
    }

    public function transaccion()
    {
        // transaccionable es la funcion de Transaccion_orm
        return $this->morphMany(AsientoContable::class, 'transaccionable');
    }

    public function linkable()
    {
       return $this->morphTo();
    }
}
