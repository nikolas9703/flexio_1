<?php
namespace Flexio\Modulo\Cobros_seguros\HttpRequest;
use Flexio\Modulo\Cobros_seguros\Models\MetodoCobro;

class FormatoMetodoCobro{


  public static function formato($elementos){

    $metodos_cobros = collect($elementos);

    $metodo = $metodos_cobros->map(function($item){

       if(method_exists(new FormatoMetodoCobro ,$item['tipo_pago'])){
          $referencia = call_user_func([new FormatoMetodoCobro,$item['tipo_pago']], $item);
          $item['referencia'] = $referencia;
       }
       $item = array_filter($item);
       return MetodoCobro::register($item);
    });
    return $metodo->all();
  }

  function cheque($item){
    $tipo = ['numero_cheque' => $item['numero_cheque'],'nombre_banco_cheque'=> $item['nombre_banco_cheque']];
    return $tipo;
  }

  function ach($item){
    $tipo = ['nombre_banco_ach' => $item['nombre_banco_ach'], 'cuenta_cliente'=> $item['cuenta_cliente']];
    return $tipo;
  }

}
