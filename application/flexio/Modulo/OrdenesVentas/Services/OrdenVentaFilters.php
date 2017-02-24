<?php
namespace Flexio\Modulo\OrdenesVentas\Services;

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Provider\QueryFilters;
use Carbon\Carbon;


class OrdenVentaFilters extends QueryFilters
{

    public function cliente($cliente)
    {
        return $this->builder->where('cliente_id', $cliente);
    }

    public function orden_trabajo($orden_trabajo)
    {
        return $this->builder
        ->select('ord_ventas.*')
        ->join('odt_ordenes_trabajo', function($join) use ($orden_trabajo){
            $join->on('odt_ordenes_trabajo.orden_de_id', '=', 'ord_ventas.id');
        })->where('odt_ordenes_trabajo.id', $orden_trabajo)
        ->where('odt_ordenes_trabajo.orden_de', 'orden_venta');
    }

    public function desde($desde)
    {
        return $this->builder->whereDate('fecha_desde','>=',Carbon::createFromFormat('d/m/Y', $desde)->format('Y-m-d'));
    }

    public function hasta($hasta)
    {
        return $this->builder->where('fecha_desde','<=',Carbon::createFromFormat('d/m/Y', $hasta)->format('Y-m-d'));
    }

    public function etapa($etapa)
    {
        return $this->builder->where('estado','=' ,$etapa);
    }

    public function vendedor($vendedor)
    {
        return $this->builder->where('created_by',$vendedor);
    }

}
