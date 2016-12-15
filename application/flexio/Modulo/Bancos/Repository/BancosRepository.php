<?php
namespace Flexio\Modulo\Bancos\Repository;

use Flexio\Modulo\Bancos\Models\Bancos as Bancos;


class BancosRepository implements BancosInterface{
    
    public function get($clause = array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
    {
        $bancos = new Bancos();
        
        if($sidx!=NULL && $sord!=NULL){$bancos->orderBy($sidx, $sord);}
        
        return $bancos->get();
    }
    
    public function find($banco_id) {
        return Bancos::find($banco_id);
    }
}
