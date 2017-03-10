<?php

namespace Flexio\Modulo\Inventarios\Services;

use Flexio\Provider\QueryFilters;

class DatoAdicionalFilters extends QueryFilters
{
    public function empresa($empresa_id)
    {
        return $this->builder->where('dat_datos_adicionales.empresa_id', $empresa_id);
    }

    public function estado($estado)
    {
        return $this->builder->where('dat_datos_adicionales.estado', $estado);
    }

    public function categoria($categoria)
    {
        return $this->builder->where(function($query) use ($categoria) {
            $query->where('dat_datos_adicionales.adicionable_id', $categoria);
            $query->where('dat_datos_adicionales.adicionable_type', 'Flexio\Modulo\Inventarios\Models\Categoria');
        });
    }
}
