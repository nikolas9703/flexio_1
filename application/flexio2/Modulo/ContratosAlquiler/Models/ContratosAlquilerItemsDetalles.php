<?php
namespace Flexio\Modulo\ContratosAlquiler\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;


class ContratosAlquilerItemsDetalles extends Model
{
    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = false;
    protected $keepRevisionOf = ['operacion_type','operacion_id','cantidad','serie','bodega_id','fecha','estado_item_devuelto'];

    protected $table    = 'contratos_items_detalles';
    protected $fillable = ['operacion_type','operacion_id','cantidad','serie','bodega_id','fecha','estado_item_devuelto'];
    protected $guarded  = ['id'];
    protected $appends  = ['fecha_format'];


    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
    }
    public static function boot() {
        parent::boot();
    }

    public function operacion()
    {
        return $this->morphTo();
    }

    public function entregas()
    {
        return $this->belongsTo('Flexio\Modulo\EntregasAlquiler\Models\EntregasAlquiler', 'operacion_id')
                ->where('operacion_type', 'Flexio\\Modulo\\EntregasAlquiler\\Models\\EntregasAlquiler');
    }

    public function devoluciones()
    {
        return $this->belongsTo('Flexio\Modulo\DevolucionesAlquiler\Models\DevolucionesAlquiler', 'operacion_id')
        ->where('operacion_type', 'Flexio\\Modulo\\DevolucionesAlquiler\\Models\\DevolucionesAlquiler');
    }
    public function getFechaAttribute($value)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $value);
    }

    public function getFechaFormatAttribute()
    {
        return $this->fecha->format('d/m/Y');
    }

    public function setFechaAttribute($date)
    {
        return $this->attributes['fecha'] = Carbon::createFromFormat('d/m/Y', $date, 'America/Panama');
    }

}
