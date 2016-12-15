<?php
namespace Flexio\Modulo\Conciliaciones\Repository;

use Flexio\Modulo\Conciliaciones\Models\Conciliaciones as Conciliaciones;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable as Transacciones;
use Flexio\Library\Util\FlexioSession;


class ConciliacionesRepository
{
    public function create($created)
    {


        $conciliacion       = $created['conciliacion'];
        $transacciones      = $created['transacciones'];
        $model_conciliacion = Conciliaciones::create($conciliacion);

        foreach($transacciones as $transaccion)
        {
            if(isset($transaccion["transaccion_id"]) and !empty($transaccion["transaccion_id"]))
            {
                $aux = Transacciones::find($transaccion["transaccion_id"]);

                $aux->conciliacion_id       = $model_conciliacion->id;
                $aux->balance_verificado    = $transaccion["balance_verificado"];
                $aux->save();
            }
        }

        return $model_conciliacion;
    }

    public function update($update){

    }

    public function count($clause = array())
    {
        $conciliaciones = Conciliaciones::deEmpresa($clause["empresa_id"]);

        $this->_filtros($conciliaciones, $clause);

        return $conciliaciones->count();
    }

    public function get($clause = array(), $sidx = null, $sord = null, $limit = null, $start = null)
    {
         $conciliaciones = Conciliaciones::deEmpresa($clause["empresa_id"]);

        $this->_filtros($conciliaciones, $clause);

        if($sidx!==null&&$sord!==null){$conciliaciones->orderBy($sidx, $sord);}
        if($limit!=null){$conciliaciones->skip($start)->take($limit);}

        return $conciliaciones->get();
    }

    private function _filtros($conciliaciones, $clause)
    {
        if(isset($clause["cuenta_id"]) and !empty($clause["cuenta_id"])){$conciliaciones->deCuenta($clause["cuenta_id"]);}
        if(isset($clause["fecha_inicio"]) and !empty($clause["fecha_inicio"])){$conciliaciones->deFechaInicio($clause["fecha_inicio"]);}
        if(isset($clause["fecha_fin"]) and !empty($clause["fecha_fin"])){$conciliaciones->deFechaFin($clause["fecha_fin"]);}
    }

    function findByUuid($uuid) {
        $session = FlexioSession::now();
        return Conciliaciones::where('uuid_conciliacion', hex2bin($uuid))->where('empresa_id',$session->empresaId())->first();
    }

}
