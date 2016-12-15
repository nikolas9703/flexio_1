<?php
namespace Flexio\Modulo\Contabilidad\Models;

trait SignoCuenta{
    public function signo($transaccion){
        $cuenta = Cuentas::find($transaccion['cuenta_id']);
        if(isset($transaccion['debito']) && in_array($cuenta->tipo_cuenta_id,[2,3,4])){
            $transaccion['debito'] = -abs($transaccion['debito']);
        }

        if(isset($transaccion['credito']) && in_array($cuenta->tipo_cuenta_id,[1,5])){
            $transaccion['credito'] = -abs($transaccion['credito']);
        }

        return  $transaccion;
    }
}
