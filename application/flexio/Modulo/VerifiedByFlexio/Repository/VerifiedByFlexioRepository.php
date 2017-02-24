<?php

namespace Flexio\Modulo\VerifiedByFlexio\Repository;

use Flexio\Modulo\VerifiedByFlexio\Models\VerifiedByFlexio;

class VerifiedByFlexioRepository
{

    public function guardar($params)
    {
        if(isset($params['id']) && !empty($params['id']))
        {
            $row = VerifiedByFlexio::find($params['id']);
            $row->update($params);
            return $row;
        }
        return VerifiedByFlexio::create($params);
    }

    public function validate_link($clause, $ip = '')
    {
        $row = VerifiedByFlexio::where(function($query) use ($clause) {
            $query->deFiltro($clause['campo']);
        })->first();

        if(count($row))
        {
            return $row->update(['link_validated' => 1, 'last_ip' => $ip]);
        }
        return false;
    }

    public function validate_document($clause, $ip = '')
    {
        $row = VerifiedByFlexio::where(function($query) use ($clause) {
            $query->deFiltro($clause['campo']);
        })->first();

        if(count($row))
        {
            return $row->update(['document_validated' => 1, 'last_ip' => $ip]);
        }
        return false;
    }

}
