<?php
namespace Flexio\Modulo\DepreciacionActivosFijos\Repository;

use Flexio\Modulo\DepreciacionActivosFijos\Models\DepreciacionActivoFijoItem;

class DepreciacionItemsRepository {

  
    public function get($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
    {
        $depreciaciones_items = DepreciacionActivoFijoItem::deEmpresa($clause["empresa_id"]);
  
        $this->_filtros($depreciaciones_items, $clause);
        
        if($sidx!=NULL && $sord!=NULL){$depreciaciones_items->orderBy($sidx, $sord);}
        if($limit!=NULL){$depreciaciones_items->skip($start)->take($limit);}
        return $depreciaciones_items->get();
    }
    
    private  function _filtros($depreciaciones_items, $clause)
    {
        if(isset($clause["codigo_serial"]) and !empty($clause["codigo_serial"])){$depreciaciones_items->deCodigoSerial($clause["codigo_serial"]);}
        if(isset($clause["item_id"]) and !empty($clause["item_id"])){$depreciaciones_items->deItem($clause["item_id"]);}
    }


}
