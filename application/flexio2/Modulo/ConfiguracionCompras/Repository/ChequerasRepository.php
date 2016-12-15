<?php
namespace Flexio\Modulo\ConfiguracionCompras\Repository;

//modelos
use Flexio\Modulo\ConfiguracionCompras\Models\Chequera as Chequeras;
use Flexio\Modulo\ConfiguracionCompras\Models\Cheques as Cheques;

class ChequerasRepository implements ChequerasInterface{
    
    private function _filtros($categorias, $clause)
    {
        if(isset($clause["conItems"]) and $clause["conItems"] === true){$categorias->conItems();}
    }
    
    public function incrementa_secuencial($chequera_id)
    {
        $chequera = Chequeras::find($chequera_id);
        $chequera->proximo_cheque += 1;
        return $chequera->save();
    }
   
    public function get($clause = array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
    {
        $categorias = Categorias::deEmpresa($clause["empresa_id"]);
        
        //filtros
        $this->_filtros($categorias, $clause);
        
        if($sidx!=NULL && $sord!=NULL){$categorias->orderBy($sidx, $sord);}
        if($limit!=NULL){$categorias->skip($start)->take($limit);}
        return $categorias->get();
    }

    function findByUuid($uuid){
        return Chequeras::where('uuid_chequera',hex2bin($uuid))->first();
    }

    function nuevoCheque($id=null){
        $chequera=Chequeras::where('id',$id)->first();
        $numero_nuevo=$chequera->proximo_cheque+1;
        $chequera->update(array("proximo_cheque"=>$numero_nuevo));
    }
}
