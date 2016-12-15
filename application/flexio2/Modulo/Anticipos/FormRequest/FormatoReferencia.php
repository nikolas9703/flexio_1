<?php
namespace Flexio\Modulo\Anticipos\FormRequest;

use Flexio\Library\Util\FormRequest;


class FormatoReferencia {


    public static function referencia($tipo, $referencia) {

        if(method_exists(new FormatoReferencia , $tipo)){
           $referencia = call_user_func([new FormatoReferencia,$tipo], $referencia);
           $resultado = $referencia;
           return $resultado;
        }
        return '';
    }


    function cheque($item){
      $tipo = ['numero_cheque' => $item['numero_cheque'],'nombre_banco_cheque'=> $item['nombre_banco_cheque']];
      return $tipo;
    }

    function ach($item){
      $tipo = ['nombre_banco_ach' => $item['nombre_banco_ach'], 'cuenta'=> $item['cuenta']];
      return $tipo;
    }

    /*function tarjeta_credito($item){
        $tipo = ['numero_tarjeta' => $item['numero_tarjeta'], 'numero_recibo'=> $item['numero_recibo']];
        return $tipo;
    }*/
}
