<?php
namespace Flexio\Modulo\ClientesPotenciales\Repository;

use Flexio\Modulo\ClientesPotenciales\Models\ClientesPotencialesCat;


class ClientesPotencialesCatRepository{
    
    public function get($clause, $sidx = NULL, $sord = NULL, $limit = NULL, $start = NULL)
    {
        $clientes_potenciales_categorias = ClientesPotencialesCat::where(function($query) use ($clause){
            $this->_filtros($query, $clause);
        });
        
        //filtros
        $this->_filtros($clientes_potenciales_categorias, $clause);

        if($sidx!=NULL && $sord!=NULL){$clientes_potenciales_categorias->orderBy($sidx, $sord);}
        if($limit!=NULL){$clientes_potenciales_categorias->skip($start)->take($limit);}

        return $clientes_potenciales_categorias->get();
    }
    
    private function _filtros($query, $clause)
    {
        if(isset($clause['id_campo']) and !empty($clause['id_campo'])){$query->whereIdCampo($clause['id_campo']);}
    }
    
}
