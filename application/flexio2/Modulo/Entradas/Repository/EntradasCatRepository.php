<?php
namespace Flexio\Modulo\Entradas\Repository;

use Flexio\Modulo\Entradas\Models\EntradasCat as EntradasCat;
use Flexio\Modulo\Inventarios\Models\Unidades as Unidades;

class EntradasCatRepository implements EntradasCatInterface{
    
    public function get($clause = array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
    {
        $entradasCat = EntradasCat::deValor($clause["valor"]);
        
        if($sidx!=NULL && $sord!=NULL){$entradasCat->orderBy($sidx, $sord);}
        if($limit!=NULL){$entradasCat->skip($start)->take($limit);}
        
        return $entradasCat->get();
    }
}
