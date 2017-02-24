<?php
namespace Flexio\Modulo\SubContratos\Repository;

use Flexio\Modulo\SubContratos\Models\SubContrato   as SubContrato;
use Flexio\Modulo\SubContratos\Models\Adenda        as Adenda;
use Flexio\Modulo\SubContratos\Models\AdendaMonto   as AdendaMonto;
use Flexio\Modulo\Comentario\Models\Comentario      as Comentario;

class AdendaRepository
{
    public function create($create)
    {
        $aux = $this->findBy($create["adenda"]);


        $array_monto = [];
        $adenda = (count($aux)) ? $aux : Adenda::create($create['adenda']);

        $adenda_monto = $create['montos'];

        //dd(collect($create["montos"])->sum('monto'));
        // Si esta editando
        if(!empty($aux) && !empty($aux->toArray())){
          $aux->update(array("monto_adenda" => !empty($create["montos"]) ? collect($create["montos"])->sum('monto'): 0));
        }

        $adenda->adenda_montos()->delete();
        foreach($adenda_monto as $monto)
        {
            $array_monto[] = new AdendaMonto($monto);
        }
        $adenda->adenda_montos()->saveMany($array_monto);
        return $adenda;
    }

    public function agregarComentario($modelId, $comentarios){
        $adenda     = Adenda::find($modelId);
        $comentario = new Comentario($comentarios);

        $adenda->comentario()->save($comentario);
        return $adenda;
  }

    public function lista_totales($clause = array())
    {
        return Adenda::where(function($query) use ($clause){
            $query->where('empresa_id', '=', $clause['empresa_id']);
            if(isset($clause['subcontrato_id'])) $query->where('subcontrato_id', '=', $clause['subcontrato_id']);
        })->count();
    }

    public function listar($clause = array(), $sidx = null, $sord = null, $limit = null, $start = null)
    {
        $subcontratos = Adenda::where(function($query) use($clause){
            $query->where('empresa_id', '=', $clause['empresa_id']);
            if(isset($clause['subcontrato_id'])) $query->where('subcontrato_id', '=', $clause['subcontrato_id']);
        });
        if($sidx !== null && $sord !== null) $subcontratos->orderBy($sidx, $sord);
        if($limit != null) $subcontratos->skip($start)->take($limit);
        return $subcontratos->get();
    }

    public function findBy($clause)
    {
        $adendas = Adenda::deEmpresa($clause["empresa_id"]);

        //filtros
        $this->_filtros($adendas, $clause);

        return $adendas->first();
    }

    private function _filtros($adendas, $clause)
    {
        if(isset($clause["uuid_adenda"]) and !empty($clause["uuid_adenda"])){$adendas->deUuid($clause["uuid_adenda"]);}
        if(isset($clause["codigo"]) and !empty($clause["codigo"])){$adendas->deCodigo($clause["codigo"]);}
    }
}
