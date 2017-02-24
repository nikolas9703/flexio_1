<?php
namespace Flexio\Modulo\Cobros\Services;

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Provider\QueryFilters;
use Carbon\Carbon as Carbon;

class CobroQueryFilters extends QueryFilters{

  function codigo($codigo){
    return $this->builder->where('codigo','like',"%".$codigo."%");
  }

  function empresa($empresa){
    return $this->builder->where('empresa_id',$empresa);
  }

  function cliente ($cliente){
    return $this->builder->where('cliente_id',$cliente);
  }

  function fecha_min($fecha){
    $fecha = Carbon::createFromFormat('d/m/Y', $fecha, 'America/Panama');
      return $this->builder->where('fecha_pago','>=',$fecha);
  }

  function fecha_max($fecha){
    $fecha = Carbon::createFromFormat('d/m/Y', $fecha, 'America/Panama');
      return $this->builder->where('fecha_pago','<=',$fecha);
  }

  function estado($estado){
      return $this->builder->where('estado',$estado);
  }
  
  function factura($factura_id) {
      return $this->builder->where('empezable_id', $factura_id)->where('empezable_type','Flexio\Modulo\FacturasVentas\Models\FacturaVenta');
  }

}
