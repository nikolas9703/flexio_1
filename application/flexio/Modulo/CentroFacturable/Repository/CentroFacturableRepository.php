<?php

namespace Flexio\Modulo\CentroFacturable\Repository;


use Flexio\Modulo\CentroFacturable\Models\CentroFacturable;


class CentroFacturableRepository{

  function find($id){
    return  CentroFacturable::findOrFail($id);
  }

    private function _filtros($query, $clause){

        $query->whereEliminado('0');
        if(isset($clause['empresa_id']) and !empty($clause['empresa_id'])){$query->whereEmpresaId($clause['empresa_id']);}
        if(isset($clause['campo']) and !empty($clause['campo'])){$query->deFiltro($clause['campo']);}

    }

    public function get($clause = [], $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
    {
        $centros_facturables = CentroFacturable::where(function($query) use ($clause){
            $this->_filtros($query, $clause);
        });

        $centros_facturables->orderBy('principal', 'desc');

        if($sidx!=NULL && $sord!=NULL) $centros_facturables->orderBy($sidx, $sord);
        if($limit!=NULL) $centros_facturables->skip($start)->take($limit);

        return $centros_facturables->get();
    }

    public function count($clause = [])
    {
        $centros_facturables = CentroFacturable::where(function($query) use ($clause){
            $this->_filtros($query, $clause);
        });

        return $centros_facturables->count();
    }

}
