<?php
namespace Flexio\Modulo\NotaCredito\Repository;
use Flexio\Modulo\NotaCredito\Models\NotaCredito as NotaCredito;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\NotaCredito\Transform\NotaCreditoItemTransformer as NotaCreditoItemTransformer;


class NotaCreditoRepository{


function crear($crear,$array_comentario = []){

    if(!isset($crear['nota_credito']['id'])){
        $nota_credito = NotaCredito::create($crear['nota_credito']);
        $comentario = new Comentario($array_comentario);
        $nota_credito->comentario()->save($comentario);
    }else{
        $nota_credito = NotaCredito::find($crear['nota_credito']['id']);
        $nota_credito->fill($crear['nota_credito']);
        $nota_credito->save();
    }
    $lineItem = new NotaCreditoItemTransformer;
    $items = $lineItem->crearInstancia($crear['items']);
    $nota_credito->items()->saveMany($items);
    return $nota_credito;
  }

  function findByUuid($uuid){
    return NotaCredito::where('uuid_nota_credito',hex2bin($uuid))->first();
  }

  function agregarComentario($modelId, $comentarios){
    $nota_credito = NotaCredito::find($modelId);
    $comentario = new Comentario($comentarios);
    $nota_credito->comentario()->save($comentario);
    return $nota_credito;
  }

  function lista_totales($clause=array()){
    return NotaCredito::where(function($query) use($clause){
      $query->where('empresa_id','=',$clause['empresa_id']);
      if(isset($clause['cliente_id']))$query->where('cliente_id','=' ,$clause['cliente_id']);
      if(isset($clause['estado']))$query->where('estado','=' ,$clause['estado']);
      if(isset($clause['creado_por']))$query->where('creado_por','=',$clause['creado_por']);
      if(isset($clause['fecha_desde']))$query->where('fecha','>=',$clause['fecha_desde']);
      if(isset($clause['fecha_hasta']))$query->where('fecha','<=',$clause['fecha_hasta']);
    })->count();
  }

  /**
  * @function de listar y busqueda
  */
  public function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){
      $ordenes = NotaCredito::where(function($query) use($clause){
          $query->where('empresa_id','=',$clause['empresa_id']);
          if(isset($clause['cliente_id']))$query->where('cliente_id','=' ,$clause['cliente_id']);
          if(isset($clause['estado']))$query->where('estado','=' ,$clause['estado']);
          if(isset($clause['creado_por']))$query->where('creado_por','=',$clause['creado_por']);
          if(isset($clause['fecha_desde']))$query->where('fecha','>=',$clause['fecha_desde']);
          if(isset($clause['fecha_hasta']))$query->where('fecha','<=',$clause['fecha_hasta']);
      });
      if($sidx!=NULL && $sord!=NULL) $ordenes->orderBy($sidx, $sord);
      if($limit!=NULL) $ordenes->skip($start)->take($limit);
    return $ordenes->get();
  }
}
