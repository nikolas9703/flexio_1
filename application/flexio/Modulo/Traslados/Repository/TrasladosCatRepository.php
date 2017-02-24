<?php
namespace Flexio\Modulo\Traslados\Repository;


//modelos
use Flexio\Modulo\Traslados\Models\TrasladoCat;

class TrasladosCatRepository
{


    public function get($clause = array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
    {
        $categorias = TrasladoCat::where(function($query) use ($clause){
            if(isset($clause['campo']) && !empty($clause['campo']) && is_array($clause['campo'])){$query->deFiltro($clause);}
        });

        if($sidx!=NULL && $sord!=NULL){$categorias->orderBy($sidx, $sord);}
        if($limit!=NULL){$categorias->skip($start)->take($limit);}

        return $categorias->get();
    }

    public function count($clause = array())
    {
        $categorias = TrasladoCat::where(function($query) use ($clause){
            if(isset($clause['campo']) && !empty($clause['campo']) && is_array($clause['campo'])){$query->deFiltro($clause);}
        });

        return $categorias->count();
    }


}
