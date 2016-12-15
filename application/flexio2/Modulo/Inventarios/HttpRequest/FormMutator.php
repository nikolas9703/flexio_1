<?php

namespace Flexio\Modulo\Inventarios\HttpRequest;

//utils
use Flexio\Library\Util\FlexioSession;

class FormMutator{

    protected $session;

    public function __construct()
    {
        $this->session = new FlexioSession;
    }

    public function item($item)
    {
        return array_merge(
            $item,
            //sobrescribe las propiedades del primer arreglo
            [
                "item_alquiler" => isset($item["item_alquiler"]) && $item["item_alquiler"] == "on" ? 1 : 0,
                "creador_por" => $this->session->usuarioId()
            ]
        );
    }

    public function unidades($unidades)
    {
        $aux = [];

        foreach($unidades as $i => $unidad)
        {
            $aux[$unidad["id_unidad"]] = [
                "base" => ($i == $unidades[0]["base"]) ?  1 : 0,
                "factor_conversion" => $unidad["factor_conversion"]
            ];
        }

        return $aux;
    }

    public function precios($precios)
    {
        $aux = [];

        foreach($precios as $precio)
        {
            $aux[$precio["id_precio"]] = [
                "precio" => $precio["precio"]
            ];
        }

        return $aux;
    }
    public function precios_alquiler($precios)
    {
         foreach($precios as $key=>$precio)
        {
           if( $precio['hora'] == '' &&
               $precio['diario'] == '' &&
               $precio['semanal'] == '' &&
               $precio['mensual'] == ''  &&
               $precio['tarifa_4_horas'] == '' &&
               $precio['tarifa_15_dias'] == ''  &&
               $precio['tarifa_28_dias'] == ''  &&
               $precio['tarifa_30_dias'] == ''
           ){
              unset($precios[$key]);
           }

        }
         return $precios;
    }
}
