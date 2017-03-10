<?php

namespace Flexio\Modulo\ConfiguracionCompras\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class TerminoCondicion extends Model
{

    protected $table = 'dat_terminos_condiciones';
    protected $fillable = ['modulo', 'descripcion', 'estado', 'content', 'empresa_id', 'created_by'];
    protected $guarded = ['id', 'uuid_termino_condicion'];
    public $timestamps = true;

    public function __construct(array $attributes = array())
    {
        $this->setRawAttributes(array_merge($this->attributes, ['uuid_termino_condicion' => Capsule::raw('ORDER_UUID(uuid())')]), true);
        parent::__construct($attributes);
    }

    public function getUuidTerminoCondicionAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function categorias()
    {
        return $this->belongsToMany('Flexio\Modulo\Inventarios\Models\Categoria', 'dat_terminos_condiciones_categorias', 'termino_id', 'categoria_id');
    }

    public function present()
    {
        return new \Flexio\Modulo\ConfiguracionCompras\Presenter\TerminoCondicionPresenter($this);
    }

    public function scopeDeFiltro($query, $campo)
    {
        $queryFilter = new \Flexio\Modulo\ConfiguracionCompras\Services\TerminoCondicionFilters();
        return $queryFilter->apply($query, $campo);
    }

}
