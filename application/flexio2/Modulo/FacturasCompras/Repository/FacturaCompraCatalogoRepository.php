<?php

namespace Flexio\Modulo\FacturasCompras\Repository;

use Flexio\Modulo\FacturasCompras\Models\FacturaCompraCatalogo;

class FacturaCompraCatalogoRepository {

    
    public function get($clause, $sidx = NULL, $sord = NULL, $limit = NULL, $start = NULL) {
        
        $factura_compra_catalogo = FacturaCompraCatalogo::where(function($query) use ($clause){
            
            $this->_filtros($query, $clause);
            
        });

        if($sidx!=NULL&&$sord!=NULL){$factura_compra_catalogo->orderBy($sidx,$sord);}
        if($limit != NULL){$factura_compra_catalogo->skip($start)->take($limit);}

        return $factura_compra_catalogo->get();
        
    }

    

    private function _filtros($query, $clause){
        
        if (isset($clause["tipo"]) and !empty($clause["tipo"])){$query->whereTipo($clause["tipo"]);}
        
    }
    

}
