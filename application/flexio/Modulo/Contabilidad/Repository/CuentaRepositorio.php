<?php

namespace Flexio\Modulo\Contabilidad\Repository;

use Flexio\Modulo\Contabilidad\Models\Cuentas;

class CuentaRepositorio {

    public $builder;

    function __construct() {
        $this->builder = (new Cuentas)->newQuery();
    }
      /**
      * @param $empresa el id de la empresa
      */
    function getCuentas($empresa = null){

        if(empty($empresa) || is_null($empresa)){
            throw new \Exception('debe tener el id de la empresa');
        }

        $this->builder->where('empresa_id',$empresa);
        return $this;
    }

    function conId($id){
        $this->builder->where('id', $id);
        return $this;
    }

    function uuid($uuid){
        if(is_array($uuid) && !empty($uuid)){
            $this->builder->whereIn('uuid_cuenta', $id);
            return $this;
        }

        $this->builder->where('uuid_cuenta', hex2bin($uuid));
        return $this;
    }

    function transaccionalesDeEmpresa($empresa_id)
    {
         $this->builder->where("contab_cuentas.empresa_id", $empresa_id)
                ->whereNotIn("id", function($q) use ($empresa_id){
                    $q->select("padre_id")
                    ->from("contab_cuentas")
                    ->where("empresa_id", $empresa_id);
                });
        return $this;        
    }

    

    function fetch(){
        return $this->builder->get();
    }
  }
