<?php

namespace Flexio\Modulo\Traslados\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class TrasladoCat extends Model
{
    protected $table = 'tras_traslados_cat';
    public $timestamps = false;
    protected $fillable = ['id_campo','valor','etiqueta'];
    protected $guarded = ['id_cat'];

    public function scopeDeFiltro($query, $campo)
    {
        $queryFilter = new \Flexio\Modulo\Traslados\Services\TrasladoCatFilters;
        return $queryFilter->apply($query, $campo);
    }
}
