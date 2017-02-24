<?php
namespace Flexio\Modulo\Cobros_seguros\Repository;
use Flexio\Modulo\Cobros_seguros\Models\Cobros_seguros as Cobro;
use Flexio\Modulo\Cobros_seguros\Models\CobroFactura as CobroFac;
use Flexio\Modulo\Comentario\Models\Comentario;

class CobroRepository {

  function find($id)
  {
    return Cobro::find($id);
  }

  function getAll($clause){
    return Cobro::where('empresa_id', '=', $clause['empresa_id'])->get();
  }

  function create($created){
    $cobro = Cobro::create($created);
    return $cobro;
  }

  function crear($crear){
      if(empty($crear['id'])){
          $cobro = Cobro::create($crear);
      }else{
          $cobro = Cobro::find($crear['id']);
          $cobro->update($crear);
      }
      return $cobro;
  }

  function update($update){
    $cobro = Cobro::find($update['id']);
    $update['monto_pagado'] = $update['monto_pagado'] + $cobro->monto_pagado;
    $cobro->update($update);
    return $cobro;
  }

  function findByUuid($uuid){
    return Cobro::where('uuid_cobro',hex2bin($uuid))->first();
  }

  function cobroFactura($factura_id){
    return CobroFac::where('factura_id',$factura_id);
  }

function lista_totales($clause=array()){
  return Cobro::where(function($query) use($clause){
    $query->where('empresa_id','=',$clause['empresa_id']);
    if(isset($clause['cliente_id']))$query->where('cliente_id','=' ,$clause['cliente_id']);
    if(isset($clause['etapa']))$query->where('estado','=' ,$clause['etapa']);
    if(isset($clause['codigo']))$query->where('codigo','like' ,"%".$clause['codigo']);
    if(isset($clause['creado_por']))$query->where('codigo','=',$clause['creado_por']);
    if(isset($clause['fecha_desde']))$query->where('fecha_pago','<=',$clause['fecha_desde']);
    if(isset($clause['fecha_hasta']))$query->where('fecha_pago','>=',$clause['fecha_hasta']);
    //Filtros para cuando se muestra la tabla
        //como subpanel en otros modulos
        if(isset($clause["caja_id"])){
                $query->where("depositable_type", "Flexio\Modulo\Cajas\Models\Cajas")->whereIn("depositable_id", array($clause["caja_id"]));
        }
        //orden venta alquiler
        if(isset($clause["orden_alquiler_id"])){
          $query->where("depositable_type", "Flexio\Modulo\OrdenesAlquiler\Models\OrdenVentaAlquiler")->whereIn("depositable_id", array($clause["orden_alquiler_id"]));
        }
  })->count();
}

/**
* @function de listar y busqueda
*/
public function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){
    $cobros = Cobro::where(function($query) use($clause){
      $query->where('empresa_id','=',$clause['empresa_id']);
      if(isset($clause['cliente_id']))$query->where('cliente_id','=' ,$clause['cliente_id']);
      if(isset($clause['id']))$query->whereIn('id', $clause['id']);
      if(isset($clause['etapa']))$query->where('estado','=' ,$clause['etapa']);
      if(isset($clause['codigo']))$query->where('codigo','like' ,"%".$clause['codigo']);
      if(isset($clause['fecha_desde']))$query->where('fecha_pago','<=',$clause['fecha_desde']);
      if(isset($clause['fecha_hasta']))$query->where('fecha_pago','>=',$clause['fecha_hasta']);
      //Filtros para cuando se muestra la tabla
        //como subpanel en otros modulos
        if(isset($clause["caja_id"])){
          $query->where("depositable_type", "Flexio\Modulo\Cajas\Models\Cajas")->whereIn("depositable_id", array($clause["caja_id"]));
        }

        //orden venta alquiler
        if(isset($clause["orden_alquiler_id"])){
          $query->where("depositable_type", "Flexio\Modulo\OrdenesAlquiler\Models\OrdenVentaAlquiler")->whereIn("depositable_id", array($clause["orden_alquiler_id"]));
        }
  });


	if($sidx!=NULL && $sord!=NULL) $cobros->orderBy($sidx, $sord);
	if($limit!=NULL) $cobros->skip($start)->take($limit);
	return $cobros->get();
}

    function agregarComentario($id, $comentarios) {
        $cobro = Cobro::find($id);
        $comentario = new Comentario($comentarios);
        $cobro->comentario_timeline()->save($comentario);
        return $cobro;
    }
}
