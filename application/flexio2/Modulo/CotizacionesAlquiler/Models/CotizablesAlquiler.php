<?php
namespace Flexio\Modulo\CotizacionesAlquiler\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;

class CotizablesAlquiler extends Model
{
    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = false;
    protected $keepRevisionOf = ['cotizacion_id', 'cotz_cotizable_id', 'cotz_cotizable_type'];

    protected $table = 'cotz_cotizables';
    protected $fillable = ['cotizacion_id', 'cotz_cotizable_id', 'cotz_cotizable_type'];
    protected $guarded = ['id'];

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public static function boot() {
        parent::boot();
    }
    public function cotizable()
    {
        return $this->morphTo('cotz_cotizable');
    }

    public function cotizacion()
    {
        return $this->belongsTo('Flexio\Modulo\CotizacionesAlquiler\Models\CotizablesAlquiler', 'cotizacion_id');
    }


}
