<?php

namespace Flexio\Modulo\Presupuesto\HttpRequest;

class FormatoItemsPresupuesto{

  function obtenerData($datos, $presupuesto){


    if(!method_exists($this, $presupuesto['tipo'])){
       throw new \Exception("La funcion no existe para esta clase " . __CLASS__);
    }

    return call_user_func_array([$this, $presupuesto['tipo'] ], [$datos,$presupuesto]);
  }

  public function periodo($datos, $presupuesto){

    $meses = array_get($datos, 'meses');
    $rows = array();
    foreach($meses as $key1 => $mes){
      foreach($meses[$key1] as $index => $total_mes){
        $rows[$index][$key1] = (float)empty($total_mes)?0.00:$this->quitarComaDecimal($total_mes);
      }
    }

    $i=0;
    foreach($datos['montos'] as $total){
      $datos[$i]['montos'] = (float)empty($total)?0.00:$this->quitarComaDecimal($total);
      $datos[$i]['info_presupuesto'] = json_encode(array('meses'=>$rows[$i]));
      $datos[$i]['empresa_id'] = $presupuesto['empresa_id'];
      $datos[$i]['centro_contable_id'] = $presupuesto['centro_contable_id'];
      $datos[$i]['usuario_id'] = $presupuesto['usuario_id'];
      $datos[$i]['cuentas_id'] = $datos['cuenta_id'][$i];
    $i++;
    }
    unset($datos['cuenta_id']);
    unset($datos['meses']);
    unset($datos['montos']);
    return $datos;
  }

  function avance($datos, $presupuesto){
    $i=0;
    foreach($datos['montos'] as $total){
      $datos[$i]['montos'] = (float)empty($total)?0.00:$this->quitarComaDecimal($total);
      $datos[$i]['porcentaje'] = (float)empty($datos['porcentaje'][$i])?0.00:$this->quitarComaDecimal($datos['porcentaje'][$i]);
      $datos[$i]['empresa_id'] = $presupuesto['empresa_id'];
      $datos[$i]['centro_contable_id'] = $presupuesto['centro_contable_id'];
      $datos[$i]['usuario_id'] = $presupuesto['usuario_id'];
      $datos[$i]['cuentas_id'] = $datos['cuenta_id'][$i];
    $i++;
    }
    unset($datos['cuenta_id']);
    unset($datos['porcentaje']);
    unset($datos['montos']);
    unset($datos['ultima_actualizacion']);
    return $datos;
  }

  function quitarComaDecimal($decimal="0.00"){
    //return preg_replace('/[^0-9,]/s', '',$decimal);
    return str_replace( ',', '', $decimal );
  }

}
