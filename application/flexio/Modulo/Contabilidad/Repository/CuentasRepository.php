<?php
namespace Flexio\Modulo\Contabilidad\Repository;

use Flexio\Modulo\Contabilidad\Models\Cuentas as Cuentas;
use Flexio\Modulo\Contabilidad\Models\CuentasCentro;

class CuentasRepository implements CuentasInterface{

    private function _filtros($cuentas, $clause)
    {

        if(isset($clause["tipo_cuenta_id"]) and !empty($clause["tipo_cuenta_id"])){$cuentas->deTipo($clause["tipo_cuenta_id"]);}
        if(isset($clause["padre_id"]) and !empty($clause["padre_id"])){$cuentas->dePadre($clause["padre_id"]);}
        if(isset($clause["transaccionales"]) and $clause["transaccionales"]){$cuentas->transaccionalesDeEmpresa($clause["empresa_id"]);}
        if(isset($clause["campo"]) and !empty($clause["campo"])){$cuentas->deFiltro($clause["campo"]);}
        if(isset($clause["nombre"]) and !empty($clause["nombre"])){$cuentas->deNombre($clause["nombre"]);}
        if(isset($clause["codigo"]) and !empty($clause["codigo"])){$cuentas->deCodigo($clause["codigo"]);}
        if(isset($clause["estado"]) and !empty($clause["estado"])){$cuentas->estadoCuenta($clause);}
        if(isset($clause["q"]) and !empty($clause["q"])){$cuentas->where(function($query) use ($clause){
            $aux = $clause['q'];
            $query->where('nombre', 'like', "%$aux%");
            $query->orWhere('codigo', 'like', "%$aux%");
        });}
    }

    public function getByCentro($clause = array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
             {
                $cuentas = CuentasCentro::deEmpresa($clause["empresa_id"])->deCentro($clause["centro_id"])->get()->load("cuentas_info");
                return $cuentas->map(function($cuenta) {
                  if(count($cuenta->cuentas_info)){
                    return [
                       'id' => $cuenta->cuentas_info->id,
                       'codigo' => $cuenta->cuentas_info->codigo,
                       'nombre' => $cuenta->cuentas_info->nombre,
                       'cuenta_id' => $cuenta->cuentas_info->id,
                    ];
                  }

                });
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

        if(isset($clause["centro_contable_id"]) and !empty($clause["centro_contable_id"])){
            $cuentas->select('contab_cuentas.*')
            ->join("contab_cuentas_centros", function ($join) use ($clause){
                $join->on("contab_cuentas_centros.cuenta_id", "=", "contab_cuentas.id");
                $join->where("contab_cuentas_centros.centro_id", "=", $clause["centro_contable_id"]);
            });
        }

        //filtros
         $this->_filtros($cuentas, $clause);

        if($sidx!=NULL && $sord!=NULL){$cuentas->orderBy($sidx, $sord);}
        if($limit!=NULL){$cuentas->skip($start)->take($limit);}

        return $cuentas->get();
    }

    public function count($clause = [])
    {
        $cuentas = Cuentas::deEmpresa($clause["empresa_id"]);
        $this->_filtros($cuentas, $clause);
        return $cuentas->count();
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
