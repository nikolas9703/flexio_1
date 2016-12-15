<?php
namespace Flexio\Library\Util;

class FormRequest{

  /**
   * data_formulario limpiar array simples
   * @param  [array] $data
   * @return [array] retorn array sin atritutos vacios
   */
  static function data_formulario($data = []){
    if(!is_array($data)){
      return null;
    }
    return array_filter($data);
  }

  /**
   * array_filter_dos_dimenciones limpia los array de dos dimenciones usados en tablas dinamicas
   * @param  [array] $data
   * @return [array] retorn array de dos dimenciones
   */
  public static function array_filter_dos_dimenciones($data){
    $resp = [];

    if(is_array($data)){
      foreach($data as $key=>$value){
        if(is_array($value)){
          $resp[] = array_filter($value);
        }
      }
    }
    return $resp;
  }
}
