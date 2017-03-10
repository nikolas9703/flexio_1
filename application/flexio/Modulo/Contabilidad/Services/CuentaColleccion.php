<?php
namespace Flexio\Modulo\Contabilidad\Services;


class CuentaColleccion{

    public static $cuentas_contable = array();

    function soloTransaccionales($cuentas){
        $this->buscarHijos($cuentas);
        return self::$cuentas_contable;
    }

    function buscarHijos($cuentas){

      foreach($cuentas as $cuenta){
        if(count($cuenta->cuentas_item) > 0){
          $this->buscarHijos($cuenta->cuentas_item);
        }else{
          array_push(self::$cuentas_contable,$this->retornarId($cuenta));
        }
    }
    }

    function retornarId($cuenta){
        return [
            'id'=> $cuenta['id']
        ];
    }

    //Flexio\Modulo\Contabilidad\Repository\ListarCuentas
    //en este archivo encuenta la function para el retorno de cuentas
}
