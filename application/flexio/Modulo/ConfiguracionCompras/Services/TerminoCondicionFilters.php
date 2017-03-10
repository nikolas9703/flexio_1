<?php

namespace Flexio\Modulo\ConfiguracionCompras\Services;

use Flexio\Provider\QueryFilters;

class TerminoCondicionFilters extends QueryFilters
{

    protected $compras_modules = ['pedidos', 'ordenes', 'facturas_compras'];
    protected $ventas_modules = ['cotizaciones', 'ordenes_ventas', 'facturas'];

    public function empresa($empresa)
    {
        return $this->builder->where('dat_terminos_condiciones.empresa_id', $empresa);
    }

    public function modulo($modulo)
    {
        return $this->builder->where('dat_terminos_condiciones.modulo', $modulo);
    }

    public function estado($estado)
    {
        return $this->builder->where('dat_terminos_condiciones.estado', $estado);
    }

    public function grupo($grupo)
    {
        if($grupo == 'ventas'){
            return $this->builder->whereIn('dat_terminos_condiciones.modulo', $this->ventas_modules);
        }
        return $this->builder->whereIn('dat_terminos_condiciones.modulo', $this->compras_modules);
    }

    public function categoria($categoria)
    {
        return $this->builder->join('dat_terminos_condiciones_categorias', function($join){
            $join->on('dat_terminos_condiciones_categorias.termino_id', '=', 'dat_terminos_condiciones.id');
        })
        ->whereIn('dat_terminos_condiciones_categorias.categoria_id', $categoria)
        ->select('dat_terminos_condiciones.*');
    }
}
