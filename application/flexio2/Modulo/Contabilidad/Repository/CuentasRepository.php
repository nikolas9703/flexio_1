<?php
namespace Flexio\Modulo\Contabilidad\Repository;

use Flexio\Modulo\Contabilidad\Models\Cuentas as Cuentas;

class CuentasRepository implements CuentasInterface{

    private function _filtros($cuentas, $clause)
    {
        if(isset($clause["tipo_cuenta_id"]) and !empty($clause["tipo_cuenta_id"])){$cuentas->deTipo($clause["tipo_cuenta_id"]);}
        if(isset($clause["padre_id"]) and !empty($clause["padre_id"])){$cuentas->dePadre($clause["padre_id"]);}
        if(isset($clause["transaccionales"]) and $clause["transaccionales"]){$cuentas->transaccionalesDeEmpresa($clause["empresa_id"]);}
    }

     public function getCollectionCuentas($cuentas) {

        return $cuentas->map(function($cuenta) {

             return [
                'id' => $cuenta->id,
                'nombre' => "{$cuenta->nombre}",
                'cuenta_id' => $cuenta->id,
             ];
        });
    }

    public function find($cuenta_id) {
        return Cuentas::find($cuenta_id);
    }


    public function findByUuid($uuid) {
        return Cuentas::where("uuid_cuenta", hex2bin($uuid))->first();
    }


    public function get($clause = array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
    {
        $cuentas = Cuentas::deEmpresa($clause["empresa_id"]);

        //filtros
        $this->_filtros($cuentas, $clause);

        if($sidx!=NULL && $sord!=NULL){$cuentas->orderBy($sidx, $sord);}
        if($limit!=NULL){$cuentas->skip($start)->take($limit);}

        return $cuentas->get();
    }

    function getAll($clause){
    	return Cuentas::where($clause)->get();
    }


    /**
    *@method catalagos_transacciones
    * @description devuelve una coleccion con formato para catalogo
    *@return /illuminate/support/collection 
    */

    function catalagos_transacciones($cuentas){
        return $cuentas->map(function($cuenta) {

             return [
                'id' => $cuenta->id,
                'nombre' => $cuenta->codigo." ".$cuenta->nombre,
                'cuenta_id' => $cuenta->id,
             ];
        });
    }
}
