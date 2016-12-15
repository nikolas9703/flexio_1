<?php
namespace Flexio\Modulo\Ajustes\Repository;

//modelos
use Flexio\Modulo\Ajustes\Models\AjustesCat as AjustesCat;

class AjustesCatRepository implements AjustesCatInterface{
    
   
    public function get($clause = array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
    {
        $ajustes_cat = AjustesCat::deValor($clause["valor"]);
        
        //filtros
        //$this->_filtros($ajustesCat, $clause);
        
        if($sidx!=NULL && $sord!=NULL){$ajustes_cat->orderBy($sidx, $sord);}
        if($limit!=NULL){$ajustes_cat->skip($start)->take($limit);}
        return $ajustes_cat->get();
    }
    
}
