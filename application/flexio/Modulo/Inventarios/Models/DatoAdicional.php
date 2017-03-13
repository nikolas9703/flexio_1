<?php

namespace Flexio\Modulo\Inventarios\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class DatoAdicional extends Model
{
    protected $table = 'dat_datos_adicionales';
    protected $fillable = ['nombre', 'requerido', 'en_busqueda_avanzada', 'estado', 'empresa_id', 'adicionable_type', 'adicionable_id', 'created_by'];
    protected $guarded = ['id', 'uuid_dato'];
    public $timestamps = true;

    public function __construct(array $attributes = array())
    {
        $this->setRawAttributes(array_merge($this->attributes, ['uuid_dato' => Capsule::raw('ORDER_UUID(uuid())')]), true);
        parent::__construct($attributes);
    }

    public function getUuidDatoAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function present()
    {
        return new \Flexio\Modulo\Inventarios\Presenter\DatoAdicionalPresenter($this);
    }

    public function scopeDeFiltro($query, $campo)
    {
        $queryFilter = new \Flexio\Modulo\Inventarios\Services\DatoAdicionalFilters();
        return $queryFilter->apply($query, $campo);
    }

}
