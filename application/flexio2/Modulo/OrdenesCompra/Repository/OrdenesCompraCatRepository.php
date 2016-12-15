<?php
namespace Flexio\Modulo\OrdenesCompra\Repository;

//utilities
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon;

//modelos
use Flexio\Modulo\OrdenesCompra\Models\OrdenesCompraCat;

class OrdenesCompraCatRepository{
    
   
    public function get($clause = array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){
        
        $ordenes_compra_cat = OrdenesCompraCat::where(function($query) use ($clause){
            $this->_filtros($query, $clause);
        });
        

        if($sidx!=NULL && $sord!=NULL){$ordenes_compra_cat->orderBy($sidx, $sord);}
        if($limit!=NULL){$ordenes_compra_cat->skip($start)->take($limit);}

        return $ordenes_compra_cat->get();
        
    }
    
    private function _filtros($query, $clause){
        
        if(isset($clause["campo_id"]) and !empty($clause["campo_id"])){$query->whereIdCampo($clause["campo_id"]);}
        
    }
    
}
