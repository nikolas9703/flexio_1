<?php
namespace Flexio\Modulo\ConfiguracionCompras\Repository;

use Flexio\Modulo\ConfiguracionCompras\Repository\ChequesInterface;
use Flexio\Modulo\ConfiguracionCompras\Models\Cheques as Cheques;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Comentario\Models\Comentario;

//repositories
use Flexio\Modulo\Pagos\Repository\PagosRepository as pagosRep;


//servicios
use Flexio\Modulo\Base\Services\Numero as Numero;

class ChequesRepository implements ChequesInterface{

    protected $pagosRep;


    public function __construct()
    {
        //repositories
        $this->pagosRep = new pagosRep();
    }

  function find($id)
  {
    return Cheques::find($id);
  }

    public function anular_cheque($cheque_id)
    {
        $cheque = Cheques::find($cheque_id);

        Capsule::transaction(function () use ($cheque){

            if($cheque->estado_id == '2')//Impresa
            {
                $this->pagosRep->anular_pago($cheque->pago->id);
            }

            $cheque->estado_id = '3';//Anulada

            /**
             * Ubicamos el metodo de pago relacionado al pago y se inicializa los valores
             */
            $metodo_pago=$cheque->pago->metodo_pago()->first();
            if(!empty($metodo_pago)){
                $metodo_pago->referencia=[
                    "numero_cheque" => "",
                    "nombre_banco_cheque" => ""
                    ];
                $metodo_pago->update();
            }

            $cheque->save();


        });

        return true;
    }
            function getAll($clause){
    return Cheques::where(function($query) use($clause){
      $query->where('empresa_id','=',$clause['empresa_id']);
      if(!empty($clause['formulario']))$query->whereIn('formulario',$clause['formulario']);
      if(!empty($clause['estado']))$query->whereIn('estado',$clause['estado']);
    })->get();
  }
    public function getCollectionCellDeItem($factura, $item_id){

        $item = $factura->items2->filter(function($item) use ($item_id){
            return $item->id == $item_id;
        })->values();//reset index

        $precio_unidad = new Numero('moneda', $item[0]->pivot->precio_unidad);
        $total          = new Numero('moneda', $item[0]->pivot->precio_total);

        $unidad = Unidades::find($item[0]->pivot->unidad_id);

        return [
            $factura->fecha_desde,
            $factura->numero_documento_enlace,
            '<a class="link">'.$factura->cliente->nombre.' '.$factura->cliente->apellido.'</a>',
            $factura->etapa_catalogo->valor,
            $item[0]->pivot->cantidad.' '.$unidad->nombre,
            $precio_unidad->getSalida(),
            $total->getSalida()
        ];
    }
              function paraCrearCobro($clause){
    return Cheques::porCobrar()->where(function($query) use($clause){
      $query->where('empresa_id','=',$clause['empresa_id']);
    })->get();
  }

  function paraVerCobro($clause){
    return Cheques::cobradoParcialCompleto()->where(function($query) use($clause){
      $query->where('empresa_id','=',$clause['empresa_id']);
    })->get();
  }


  function cobradoCompletoSinNotaCredito($clause){
      return Cheques::cobradoCompleto()->has('nota_credito','<', 1)->where(function($query) use($clause){
        $query->where('empresa_id','=',$clause['empresa_id']);
      })->get();
  }

  function sinDevolucion($clause){
    return Cheques::has('devolucion','=',0)->where(function($query) use($clause){
      $query->where('empresa_id','=',$clause['empresa_id']);
      if(!empty($clause['formulario']))$query->whereIn('formulario',$clause['formulario']);
      if(!empty($clause['estado']))$query->whereIn('estado',$clause['estado']);
    })->get();
  }

  function create($created){
    $cheque = Cheques::create($created);

      return $cheque;
  }


    public function crear($crear)
    {
        if(empty($crear['cheque_id'])){
            $cheque = Cheques::create($crear);
        }else{
            $cheque = Cheques::find($crear['cheque_id']);
            $cheque->update($crear);
        }
        return $cheque;
    }
    public function editar($edicion) {
      /*echo "EDITANDO....";
      dd($edicion);*/
        return Cheques::where("id", $edicion['id'])->update($edicion);
    }
  function update($uuid=null,$campos=null){

      $cheque_full = Cheques::where('uuid_cheque',hex2bin($uuid))->first();
      $cheque = Cheques::find($cheque_full->id);
    $cheque->update($campos);
    return $cheque;
  }

  function findByUuid($uuid){
    return Cheques::where('uuid_cheque',hex2bin($uuid))->first();
  }
    private function _filtros($query, $clause)
    {
        if(isset($clause["item_id"]) and !empty($clause["item_id"])){$query->deItem($clause["item_id"]);}
    }

function lista_totales($clause=array()){

  return Cheques::leftJoin('pag_pagos', 'che_cheques.pago_id', '=', 'pag_pagos.id')->where(function($query) use($clause){
    $query->where('che_cheques.empresa_id','=',$clause['empresa_id']);
    $this->_filtros($query, $clause);
    if(isset($clause['proveedor']))$query->where('pag_pagos.proveedor_id','=' ,$clause['proveedor']);
    if(isset($clause['chequera_id']))$query->where('che_cheques.chequera_id','=' ,$clause['chequera_id']);
    if(isset($clause['estado']))$query->where('che_cheques.estado_id','=' ,$clause['estado']);
    if(isset($clause['numero']))$query->where('che_cheques.numero','like' ,"%".$clause['numero']."%");
    if(isset($clause['fecha_desde']))$query->where('che_cheques.fecha_cheque','>=',$clause['fecha_hasta']);
    if(isset($clause['fecha_hasta']))$query->where('che_cheques.fecha_cheque','<=',$clause['fecha_desde']);
  })->count();
}

/**
* @function de listar y busqueda
*/
public function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){
  $cheques = Cheques::select('che_cheques.id','che_cheques.uuid_cheque','che_cheques.numero','che_cheques.monto','che_cheques.chequera_id','che_cheques.pago_id','che_cheques.fecha_cheque','che_cheques.empresa_id','che_cheques.updated_at','che_cheques.created_at','che_cheques.estado_id')->leftJoin('pag_pagos', 'che_cheques.pago_id', '=', 'pag_pagos.id')->where(function($query) use($clause){
      $query->where('che_cheques.empresa_id','=',$clause['empresa_id']);
      $this->_filtros($query, $clause);
      if(isset($clause['proveedor']))$query->where('pag_pagos.proveedor_id','=' ,$clause['proveedor']);
      if(isset($clause['chequera_id']))$query->where('che_cheques.chequera_id','=' ,$clause['chequera_id']);
      if(isset($clause['estado']))$query->where('che_cheques.estado_id','=' ,$clause['estado']);
      if(isset($clause['numero']))$query->where('che_cheques.numero','like' ,"%".$clause['numero']."%");
      if(isset($clause['fecha_desde']))$query->where('che_cheques.fecha_cheque','>=',$clause['fecha_hasta']);
      if(isset($clause['fecha_hasta']))$query->where('che_cheques.fecha_cheque','<=',$clause['fecha_desde']);
  });
  if($sidx!=NULL && $sord!=NULL) $cheques->orderBy($sidx, $sord);
  if($limit!=NULL) $cheques->skip($start)->take($limit);
return $cheques->get();
}

    function agregarComentario($id, $comentarios) {
        $cheques = Cheques::find($id);
        $comentario = new Comentario($comentarios);
        $cheques->comentario_timeline()->save($comentario);
        return $cheques;
    }
}
