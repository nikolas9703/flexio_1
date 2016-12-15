<?php
namespace Flexio\Library\Util;

class GenerarCodigo{

/**
  * genera el codigo para la base de datos.
  *
  * @param letra  $letra inicio letra del codigo.
  * @param numero $numero  el numero que va hacer generado.
  * @param len_numero $len_numero  la longitud del numero.
  * @param inicia $inicia numero con que inicia la secuencia.
  *
  * @return string retorna el codigo generado.
  */

    public static function setCodigo($letra='',$numero=0, $len_numero=6, $inicia='0'){
        $codigo = str_pad($numero, $len_numero, $inicia, STR_PAD_LEFT);
	    return $letra.$codigo;
	}

}
