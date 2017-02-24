<?php
namespace Flexio\Modulo\SubContratos\Models;

use Illuminate\Database\Eloquent\Model       as Model;
use Illuminate\Database\Capsule\Manager      as Capsule;
use Flexio\Modulo\SubContratos\Models\Adenda as Adenda;
use Carbon\Carbon                            as Carbon;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;

class AdendaMonto extends Model
{
    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = ['empresa_id', 'monto', 'descripcion', 'cuenta_id'];

    protected $table = 'sub_adendas_montos';

    protected $fillable = ['empresa_id', 'monto', 'descripcion', 'cuenta_id'];

     protected $casts = [
        'monto' => 'float'
    ];

    protected $guarded = ['id', 'adenda_id'];

    public function adenda()
    {
        return $this->belongsTo(Adenda::class, 'adenda_id');
    }
    /**
     * Register any other events for your application.
     *
     * @return void
     */
     public function cuenta() {
         return $this->belongsTo('Flexio\Modulo\Contabilidad\Models\Cuentas','cuenta_id')->select(['id','codigo','nombre']);
     }

    public static function boot() {
        parent::boot();
    }

}
