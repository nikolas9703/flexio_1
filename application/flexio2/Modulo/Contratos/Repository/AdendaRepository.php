<?php
namespace Flexio\Modulo\Contratos\Repository;
use Flexio\Modulo\Contratos\Models\Contrato         as Contrato;
use Flexio\Modulo\Contratos\Models\Adenda           as Adenda;
use Flexio\Modulo\Contratos\Models\AdendaMonto      as AdendaMonto;
use Flexio\Modulo\Comentario\Models\Comentario      as Comentario;

class AdendaRepository{


    public function create($create)
    {
        $aux = $this->findBy($create["adenda"]);
        
        
        $array_monto = [];
        $adenda = (count($aux)) ? $aux : Adenda::create($create['adenda']);
        
        $adenda_monto = $create['montos'];

        $adenda->adenda_montos()->delete();
        foreach($adenda_monto as $monto)
        {
            $array_monto[] = new AdendaMonto($monto);
        }
        $adenda->adenda_montos()->saveMany($array_monto);
        return $adenda;
    }

  function lista_totales($clause=array())
  {
    return Adenda::where(function($query) use($clause){
      $query->where('empresa_id','=',$clause['empresa_id']);
      if(isset($clause['contrato_id']))$query->where('contrato_id','=' ,$clause['contrato_id']);
    })->count();
  }

  function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
  {
    $contratos = Adenda::where(function($query) use($clause){
        $query->where('empresa_id','=',$clause['empresa_id']);
        if(isset($clause['contrato_id']))$query->where('contrato_id','=' ,$clause['contrato_id']);
    });
    if($sidx!==NULL && $sord!==NULL) $contratos->orderBy($sidx, $sord);
    if($limit!=NULL) $contratos->skip($start)->take($limit);
    return $contratos->get();
  }
    public function findBy($clause)
    {
        $adendas = Adenda::deEmpresa($clause["empresa_id"]);
        
        //filtros
        $this->_filtros($adendas, $clause);
        
        return $adendas->first();
    }
    
    public function agregarComentario($modelId, $comentarios){
        $adenda     = Adenda::find($modelId);
        $comentario = new Comentario($comentarios);
        
        $adenda->comentario()->save($comentario);
        return $adenda;
    }
    
    private function _filtros($adendas, $clause)
    {
        if(isset($clause["uuid_adenda"]) and !empty($clause["uuid_adenda"])){$adendas->deUuid($clause["uuid_adenda"]);}
        if(isset($clause["codigo"]) and !empty($clause["codigo"])){$adendas->deCodigo($clause["codigo"]);}
    }
}
