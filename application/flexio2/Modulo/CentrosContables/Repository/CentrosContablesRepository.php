<?php
namespace Flexio\Modulo\CentrosContables\Repository;

use Flexio\Modulo\CentrosContables\Models\CentrosContables as CentrosContables;

class CentrosContablesRepository{

    private function _filtros($centros_contables, $clause)
    {
        if(isset($clause["transaccionales"]) and $clause["transaccionales"] === true){$centros_contables->transaccionales($clause["empresa_id"]);}
    }
    
    public function getCollectionCentrosContables($centros_contables){

        return $centros_contables->map(function($centro_contable){
            return [
                'id' => $centro_contable->uuid_centro,
                'nombre' => $centro_contable->nombre,
                'centro_contable_id' => $centro_contable->id
            ];
        });
        
    }

    public function findByUuid($uuid_centro_contable)
    {
        return CentrosContables::where("uuid_centro", hex2bin($uuid_centro_contable))->first();
    }

    public function get($clause = array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
    {
        $centros_contables = CentrosContables::deEmpresa($clause["empresa_id"]);

        //filtros
        $this->_filtros($centros_contables, $clause);

        if($sidx!=NULL && $sord!=NULL){$centros_contables->orderBy($sidx, $sord);}
        if($limit!=NULL){$centros_contables->skip($start)->take($limit);}
        return $centros_contables->get();
    }
    function find($id){
      return CentrosContables::find($id);
    }
}
