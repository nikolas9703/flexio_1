<?php
namespace Flexio\Modulo\Devoluciones\Repository;
use Flexio\Modulo\Devoluciones\Models\Devolucion as Devolucion;
use Flexio\Modulo\Devoluciones\Models\DevolucionCatalogo as DevolucionCatalogo;
use Flexio\Modulo\Cotizaciones\Models\LineItemTransformer as LineItemTransformer;

class DevolucionRepository {

  function find($id)
  {
    return Devolucion::find($id);
  }

  function getAll($clause){
    return Devolucion::where('empresa_id', '=', $clause['empresa_id'])->get();
  }

  function create($created){
    $devolucion = Devolucion::create($created['devolucion']);
    $lineItem = new LineItemTransformer;
    $items = $lineItem->crearInstancia($created['lineitem']);
    $devolucion->items()->saveMany($items);

    return $devolucion;
  }

  function crear($crear){
      if(empty($crear['id'])){
          $devolucion= Devolucion::create($crear);
      }else{
          $devolucion = Devolucion::find($crear['id']);
          $devolucion->update($crear);
      }
      return $devolucion;
  }

  function update($update){
    $devolucion = Devolucion::find($update['devolucion']['id']);
    $devolucion->update($update['devolucion']);
    $lineItem = new LineItemTransformer;
    $items = $lineItem->crearInstancia($update['lineitem']);
    $devolucion->items()->saveMany($items);
    return $devolucion;
  }

  function findByUuid($uuid){
    return Devolucion::where('uuid_devolucion',hex2bin($uuid))->first();
  }

function lista_totales($clause=array()){
  return Devolucion::where(function($query) use($clause){
    $query->where('empresa_id','=',$clause['empresa_id']);
    if(isset($clause['cliente_id']))$query->where('cliente_id','=' ,$clause['cliente_id']);
    if(isset($clause['etapa']))$query->where('estado','=' ,$clause['etapa']);
    if(isset($clause['creado_por']))$query->where('created_by','=',$clause['creado_por']);
    if(isset($clause['fecha_desde']))$query->where('fecha_devolucion','<=',$clause['fecha_desde']);
    if(isset($clause['fecha_hasta']))$query->where('fecha_devolucion','>=',$clause['fecha_hasta']);
  })->count();
}

/**
* @function de listar y busqueda
*/
public function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){
  $devoluciones = Devolucion::where(function($query) use($clause){
      $query->where('empresa_id','=',$clause['empresa_id']);
      if(isset($clause['cliente_id']))$query->where('cliente_id','=' ,$clause['cliente_id']);
      if(isset($clause['etapa']))$query->where('estado','=' ,$clause['etapa']);
      if(isset($clause['creado_por']))$query->where('created_by','=',$clause['creado_por']);
      if(isset($clause['fecha_desde']))$query->where('fecha_devolucion','<=',$clause['fecha_desde']);
      if(isset($clause['fecha_hasta']))$query->where('fecha_devolucion','>=',$clause['fecha_hasta']);
  });
  if($sidx!=NULL && $sord!=NULL) $devoluciones->orderBy($sidx, $sord);
  if($limit!=NULL) $devoluciones->skip($start)->take($limit);
  return $devoluciones->get();
}
}
