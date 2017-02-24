<?php

namespace Flexio\Modulo\VerifiedByFlexio\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class VerifiedByFlexio extends Model
{

    protected $table = 'sec_verified_by_flexio';

    protected $fillable = ['link_hash','document_hash','link_validated','document_validated','last_ip','created_at','updated_at'];
    protected $guarded = ['id'];


    public function scopeDeFiltro($query, $campo)
    {
        $queryFilter = new \Flexio\Modulo\VerifiedByFlexio\Services\VerifiedByFlexioFilters;
        return $queryFilter->apply($query, $campo);
    }

}
