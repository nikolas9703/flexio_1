<?php
namespace Flexio\Modulo\DepreciacionActivosFijos\Repository;
use Flexio\Modulo\DepreciacionActivosFijos\Models\DepreciacionActivoFijo as DepreciacionActivoFijo;
use Flexio\Modulo\DepreciacionActivosFijos\Transform\DepreciacionItemTransformer as DepreciacionItemTransformer ;
//use Flexio\Modulo\DepreciacionActivosFijos\Models\DevolucionCatalogo as DevolucionCatalogo;

class DepreciacionRepository {

  function find($id)
  {
    return DepreciacionActivoFijo::find($id);
  }

  function getAll($clause){
    return DepreciacionActivoFijo::where('empresa_id', '=', $clause['empresa_id'])->get();
  }

  function crear($crear){
      
      if(!isset($crear['depreciacion']['id'])){
          $depreciacion = DepreciacionActivoFijo::create($crear['depreciacion']);
      }else{
          $depreciacion = DepreciacionActivoFijo::find($crear['depreciacion']['id']);
          $depreciacion->update($crear['depreciacion']);
      }
      $lineItem = new DepreciacionItemTransformer;
      $items = $lineItem->crearInstancia($crear['items']);
      $depreciacion->items()->saveMany($items);
      return $depreciacion;
  }

  function findByUuid($uuid){
    return DepreciacionActivoFijo::where('uuid_depreciacion',hex2bin($uuid))->first();
  }

function lista_totales($clause=array()){
  return DepreciacionActivoFijo::where(function($query) use($clause){
    $query->where('empresa_id','=',$clause['empresa_id']);
    if(isset($clause['centro_contable_id']))$query->where('centro_contable_id','=' ,$clause['centro_contable_id']);
    if(isset($clause['referencia']))$query->where('referencia','like', "%".$clause['referencia']."%");
    if(isset($clause['fecha_desde']))$query->where('created_at','>=',$clause['fecha_desde']);
    if(isset($clause['fecha_hasta']))$query->where('created_at','<=',$clause['fecha_hasta']);
  })->count();
}

/**
* @function de listar y busqueda
*/
public function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){
  $depreciaciones = DepreciacionActivoFijo::where(function($query) use($clause){
      $query->where('empresa_id','=',$clause['empresa_id']);
      if(isset($clause['centro_contable_id']))$query->where('centro_contable_id','=' ,$clause['centro_contable_id']);
      if(isset($clause['referencia']))$query->where('referencia','like', "%".$clause['referencia']."%");
      if(isset($clause['fecha_desde']))$query->where('created_at','>=',$clause['fecha_desde']);
      if(isset($clause['fecha_hasta']))$query->where('created_at','<=',$clause['fecha_hasta']);
  });
  if($sidx!=NULL && $sord!=NULL) $depreciaciones->orderBy($sidx, $sord);
  if($limit!=NULL) $depreciaciones->skip($start)->take($limit);
  return $depreciaciones->get();
}


}
