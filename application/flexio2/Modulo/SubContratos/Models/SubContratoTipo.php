<?php
namespace Flexio\Modulo\SubContratos\Models;

use Illuminate\Database\Eloquent\Model            as Model;
use Illuminate\Database\Capsule\Manager           as Capsule;
use Flexio\Modulo\SubContratos\Models\SubContrato as SubContrato;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;

class SubContratoTipo extends Model
{
    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = [
        'cuenta_id',
        'monto',
        'subcontrato_id',
        'empresa_id',
        'tipo',
        'porcentaje'
    ];

    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'sub_subcontratos_tipos';

    /**
     * Estos atributos son asignables
     *
     * @var array
     */
    protected $fillable = [
    	'cuenta_id',
    	'monto',
    	'subcontrato_id',
    	'empresa_id',
    	'tipo',
    	'porcentaje'
    ];

    protected $casts = [
        'monto' => 'float',
        'porcentaje' => 'float',
    ];

    /**
     * Estos atributos no son asignables
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function subcontrato()
    {
        return $this->belongsTo(SubContrato::class, 'subcontrato_id');
    }
    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public static function boot() {
        parent::boot();
    }

    public function setMontoAttribute($value)
    {
        $this->attributes['monto'] = str_replace(',', '', $value);
    }

    public function setPorcentajeAttribute($value)
    {
        $this->attributes['porcentaje'] = str_replace(',', '', $value);
    }
}
