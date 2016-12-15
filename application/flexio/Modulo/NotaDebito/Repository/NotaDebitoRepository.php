<?php
namespace Flexio\Modulo\NotaDebito\Repository;
use Flexio\Modulo\NotaDebito\Models\NotaDebito as NotaDebito;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\NotaDebito\Transform\NotaDebitoItemTransformer as NotaDebitoItemTransformer;

//utils
use Flexio\Modulo\NotaDebito\Validators\NotaDebitoValidator;

class NotaDebitoRepository {

  protected $NotaDebitoValidator;

  public function __construct()
  {
    $this->NotaDebitoValidator = new NotaDebitoValidator;
  }

  public function crear($crear,$array_comentario = [])
  {
    $this->NotaDebitoValidator->post_validate($crear);
    if(!isset($crear['nota_debito']['id'])){
        $nota_debito = NotaDebito::create($crear['nota_debito']);
        $comentario = new Comentario($array_comentario);
        $nota_debito->comentario()->save($comentario);
    }else{
        $nota_debito = NotaDebito::find($crear['nota_debito']['id']);
        $nota_debito->update($crear['nota_debito']);
    }
    $lineItem = new NotaDebitoItemTransformer;
    $items = $lineItem->crearInstancia($crear['items']);
    $nota_debito->items()->saveMany($items);
    return $nota_debito;
  }

  public function getCollectionNotaDebito($nota_debito)
  {
      return Collect(
        array_merge(
            $nota_debito->toArray(),
            [
                'saldo_proveedor' => 0,
                'credito_proveedor' => 0,
                'filas' => $nota_debito->items,
                'landing_comments' => $nota_debito->landing_comments
            ]
        )
      );
  }

  function findByUuid($uuid){
    return NotaDebito::where('uuid_nota_debito',hex2bin($uuid))->first();
  }

  function agregarComentario($modelId, $comentarios){
    $nota_debito = NotaDebito::find($modelId);
    $comentario = new Comentario($comentarios);
    $nota_debito->comentario()->save($comentario);
    return $nota_debito;
  }

  function lista_totales($clause=array()){
    return NotaDebito::where(function($query) use($clause){
      $query->where('empresa_id','=',$clause['empresa_id']);
      if(isset($clause['centros_contables']))$query->whereIn('compra_nota_debitos.centro_contable_id' ,$clause['centros_contables']);
      if(isset($clause['proveedor_id']))$query->where('proveedor_id','=' ,$clause['proveedor_id']);
      if(isset($clause['estado']))$query->where('estado','=' ,$clause['estado']);
      if(isset($clause['codigo']))$query->where('codigo','=' ,$clause['codigo']);
      if(isset($clause['no_nota_credito']))$query->where('no_nota_credito','=' ,$clause['no_nota_credito']);
      if(isset($clause['creado_por']))$query->where('creado_por','=',$clause['creado_por']);
      if(isset($clause['fecha_desde']))$query->where('fecha','>=',$clause['fecha_desde']);
      if(isset($clause['fecha_hasta']))$query->where('fecha','<=',$clause['fecha_hasta']);
      if(isset($clause['montos_de']))$query->where('total','>=' ,$clause['montos_de']);
      if(isset($clause['montos_a']))$query->where('total','<=' ,$clause['montos_a']);
    })->count();
  }
  /**
  * @function de listar y busqueda
  */
  public function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){
      $ordenes = NotaDebito::where(function($query) use($clause){
          $query->where('empresa_id','=',$clause['empresa_id']);
          if(isset($clause['centros_contables']))$query->whereIn('compra_nota_debitos.centro_contable_id' ,$clause['centros_contables']);
          if(isset($clause['proveedor_id']))$query->where('proveedor_id','=' ,$clause['proveedor_id']);
          if(isset($clause['estado']))$query->where('estado','=' ,$clause['estado']);
          if(isset($clause['codigo']))$query->where('codigo','=' ,$clause['codigo']);
          if(isset($clause['no_nota_credito']))$query->where('no_nota_credito','=' ,$clause['no_nota_credito']);
          if(isset($clause['creado_por']))$query->where('creado_por','=',$clause['creado_por']);
          if(isset($clause['fecha_desde']))$query->where('fecha','>=',$clause['fecha_desde']);
          if(isset($clause['fecha_hasta']))$query->where('fecha','<=',$clause['fecha_hasta']);
          if(isset($clause['montos_de']))$query->where('total','>=' ,$clause['montos_de']);
          if(isset($clause['montos_a']))$query->where('total','<=' ,$clause['montos_a']);
      });
      if($sidx!=NULL && $sord!=NULL) $ordenes->orderBy($sidx, $sord);
      if($limit!=NULL) $ordenes->skip($start)->take($limit);
    return $ordenes->get();
  }
}
