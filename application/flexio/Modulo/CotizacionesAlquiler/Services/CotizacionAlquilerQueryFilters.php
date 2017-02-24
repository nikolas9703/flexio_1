<?php
namespace Flexio\Modulo\CotizacionesAlquiler\Services;

use Flexio\Provider\QueryFilters;
use Carbon\Carbon as Carbon;

class CotizacionAlquilerQueryFilters extends QueryFilters{

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

  function creado_por($usuario_id){
    return $this->builder->where('creado_por',$usuario_id);
  }

  function tipo($tipo){
    return $this->builder->where('tipo',$tipo);
  }

  function centro_contable_id($centro){
    return $this->builder->where('centro_contable_id',$centro);
  }
  
  function contrato_alquiler($contrato_alquiler_id) {
      
     return $this->builder->whereHas('contratos_de_alquiler',function($query)use($contrato_alquiler_id){
         $query->where('id', $contrato_alquiler_id);
     });
  }


}
