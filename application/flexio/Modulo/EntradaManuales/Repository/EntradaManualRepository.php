<?php
namespace Flexio\Modulo\EntradaManuales\Repository;

use Flexio\Modulo\EntradaManuales\Models\EntradaManual;

class EntradaManualRepository
{
  function findByUuid($uuid){
    return EntradaManual::findByUuid($uuid);
  } 

  public function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){
	    $query = EntradaManual::where($clause);
	    if($sidx!=NULL && $sord!=NULL) $query->orderBy($sidx, $sord);
	    if($limit!=NULL) $query->skip($start)->take($limit);
      return $query->get();
  }
  
  public static function exportar($clause=array())
	{
		$query = EntradaManual::where(function($query) use($clause){
			if(!empty($sidx) && preg_match("/cargo/i", $sidx)){
				$query->orderBy("nombre", $sord);
			}
		});
		$query->whereIn("id", $clause["id"]);
		return $query->get();
                
               
	}
}
