<?php
namespace Flexio\Modulo\Inventarios\Repository;

//modelos
use Flexio\Modulo\Inventarios\Models\ItemsCat as ItemsCat;

class itemsCatRepository implements ItemsCatInterface{

    private function _filtros($query, $clause)
    {
        if(isset($clause["tipo"]) && !empty($clause["tipo"])){$query->deValor($clause["tipo"]);}
        if(isset($clause["valor"]) && !empty($clause["valor"])){$query->deValor($clause["valor"]);}
    }

    public function get($clause = array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
    {
        $items_cat = ItemsCat::where(function($query) use ($clause){
            $this->_filtros($query, $clause);
        });

        if($sidx!=NULL && $sord!=NULL){$items_cat->orderBy($sidx, $sord);}
        if($limit!=NULL){$items_cat->skip($start)->take($limit);}
        return $items_cat->get();
    }

}
