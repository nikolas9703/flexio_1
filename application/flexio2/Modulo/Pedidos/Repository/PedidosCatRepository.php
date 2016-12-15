<?php namespace Flexio\Modulo\Pedidos\Repository;

use Flexio\Modulo\Pedidos\Models\PedidosCat;
 
class PedidosCatRepository{
    
    
    private function _filtros($query, $clause){
        
        if(isset($clause['campo_id']) and !empty($clause['campo_id'])){$query->whereIdCampo($clause['campo_id']);}
        
    }
    
    public function get($clause = []){
        
        $pedidos_cat = PedidosCat::where(function($query) use ($clause){
            
            $this->_filtros($query, $clause);
            
        });
        
        return $pedidos_cat->get();
        
    }
    
    
  
  

}
