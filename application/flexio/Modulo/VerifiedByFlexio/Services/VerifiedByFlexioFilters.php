<?php
namespace Flexio\Modulo\VerifiedByFlexio\Services;

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Provider\QueryFilters;
use Carbon\Carbon;


class VerifiedByFlexioFilters extends QueryFilters
{

    public function token($token)
    {
        return $this->builder->where('link_hash', $token);
    }

    public function document_hash($document_hash)
    {
        return $this->builder->where('document_hash', $document_hash);
    }

}
