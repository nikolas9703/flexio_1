<?php
namespace Flexio\Modulo\Salidas\Repository;

use Flexio\Modulo\Salidas\Models\SalidasCat as SalidasCat;

class SalidasCatRepository implements SalidasCatInterface{
    
    public function get($clause = array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
    {
        $salidasCat = SalidasCat::deValor($clause["valor"]);
        
        if($sidx!=NULL && $sord!=NULL){$salidasCat->orderBy($sidx, $sord);}
        if($limit!=NULL){$salidasCat->skip($start)->take($limit);}
        
        return $salidasCat->get();
    }
}
