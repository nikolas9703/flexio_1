<?php
namespace Flexio\Library\Util;

class Utiles{

  public static function generate_ramdom_token() {
		return md5(uniqid(mt_rand(), TRUE));
	}

  /**
	 * Buscar un valor en un arreglo multidimensional
	 * y retornar el key del valor encontrado.
	 *
	 * @param string $search_for
	 * @param array $search_in
	 * @return boolean
	 */
	public static function multiarray_buscar_valor($searchfor, $field, $array) {
		foreach($array AS $key => $item)
	   	{
	   		if(empty($item[$field])){
	   			continue;
	   		}
	   		if (preg_match_all("/$searchfor/im", $item[$field])) {
          return $key;
	   		}
	   	}
	   	return "";
	}

  public static function multiarray_buscar_key($array, $keySearch){
      foreach ($array as $key => $item) {
        if ($key===$keySearch) {
            return true;
        }
        else {
            if (is_array($item) && self::multiarray_buscar_key($item, $keySearch)) {
               return true;
            }
        }
    }
    return false;
  }

  public static function generar_codigo($letra='',$numero=0, $len_numero=6, $inicia='0') {
    $codigo = str_pad($numero,$len_numero,$inicia, STR_PAD_LEFT);
    return $letra.$codigo;
	}

  /**
	 * Verifica si un arreglo es
	 * bidimensional.
	 *
	 * @param array $array
	 * @return boolean
	 */
	static function is_two_dimensional($array) {
		$rv = array_filter($array, 'is_array');
		if(count($rv)>0) return true;
		return false;
	}
}
