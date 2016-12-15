<?php
namespace Flexio\Modulo\Inventarios\Repository;

use Flexio\Modulo\Inventarios\Models\LinesItems as LinesItems;

class LinesItemsRepository implements LinesItemsInterface{
    
    public function get($clause = array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
    {
        $linesItems = LinesItems::where("empresa_id", $clause["empresa_id"]);
        
        if($sidx!=NULL && $sord!=NULL){$linesItems->orderBy($sidx, $sord);}
        if($limit!=NULL){$linesItems->skip($start)->take($limit);}
        
        return $linesItems->get();
    }
    
    public function find($line_item_id)
    {
        return LinesItems::find($line_item_id);
    }
}
