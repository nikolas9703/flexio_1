<?php defined('BASEPATH') || exit('No direct script access allowed');

require_once dirname(__FILE__) . '/PHPExcel/Calculation/Statistical.php';

/**
 * Util Class
 *
 * Contiene distintas funciones utiles.
 *
 *
 * @package    PensaApp
 * @subpackage Library
 * @category   Libraries
 * @author     Pensanomica Team
 * @link       http://www.pensanomica.com
 * @since     Version 1.0
 */
class Util
{
	protected static $ci;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct() {
    	//Instancia del core de CI
    	self::$ci =& get_instance();

    	self::$ci->load->helper(array('inflector'));
    }

    /**
     * Verificar si un arreglo multidimensional
     * Tiene valores vacios
     *
     * @param  array $array
     * @return boolean
     */
   static function is_array_empty($array) {
    	if (is_array($array)) {
    		$empty = TRUE;
    		array_walk_recursive($array, function($item) use (&$empty) {
    			$empty = $empty && empty($item);
    		});
    	} else {
    		$empty = empty($array);
    	}
    	return $empty;
    }

    /**
     * Verifica una fecha de cuando es la fecha (Ayer, hoy, maÃƒÆ’Ã‚Â±ana, etc)
      *
     * @param  string Date
     * @return string
     */
    static function dia_vencido_novencido($date) {

    	$ahora = date("Y-m-d H:i:s");
    	$media_noche = date("Y-m-d", time()+86400)." 00:0:00";


     	if($date < $ahora){
    		$retorno  =  'dias atras';
    	}
    	else if($date >$media_noche){
    		$retorno  =  'futuro';
    	}
    	else if($date < $media_noche)  {
    		 $retorno  =  'hoy';
    	}
    	return $retorno;
    }


	/**
	 * Buscar un valor y retornar el indice
	 *
	 * @param  array  $array  [description]
	 * @param  [type] $search [description]
	 * @return [type]         [description]
	 */
	static function array_search_key(array $array, $search) {
		if(!is_array($array) || empty($array)){ return false; }

		foreach ($array AS $key => $value) {
			if(empty($array[$key])){ continue; }

			if(array_key_exists($search, $array[$key])){
				return $key;
				break;
			}
		}
	}

	/**
	 * Insert an array into the middle of an array
	 * http://blog.leenix.co.uk/2010/03/php-insert-into-middle-of-array.html
	 *
	 * @param  [type] &$array   [description]
	 * @param  [type] $insert   [description]
	 * @param  [type] $position [description]
	 * @return [type]           [description]
	 */
	static function array_insert(&$array, $insert, $position) {
		settype($array, "array");
		settype($insert, "array");
		settype($position, "int");

		//if pos is start, just merge them
		if($position==0) {
			$array = array_merge($insert, $array);
		} else {

			//if pos is end just merge them
			/*if($position >= (count($array)-1)) {
			 $array = array_merge($array, $insert);
			} else {*/
			//split into head and tail, then merge head+inserted bit+tail
			$head = array_slice($array, 0, $position);
			$tail = array_slice($array, $position);
			$array = array_merge($head, $insert, $tail);
			//}
		}
	}

	/**
	 * Buscar un valor en un arreglo multidimensional
	 * y retornar el key del valor encontrado.
	 *
	 * @param string $search_for
	 * @param array $search_in
	 * @return boolean
	 */
	static function multiarray_buscar_valor($searchfor, $field, $array) {
		if (!empty($array)){
			foreach($array AS $key => $item)
		   	{
		   		if(empty($item[$field])){
		   			continue;
		   		}

		   		if (preg_match("/$searchfor/i", $item[$field])) {
		   			return $key;
		   		}
		   	}
		}
	   	return "";
	}


	/**
	 * Convert stdClass Object to Array
	 */

	static function arrayCastRecursive($array) {
	    if (is_array($array)) {
	        foreach ($array as $key => $value) {
	            if (is_array($value)) {
	                $array[$key] = self::arrayCastRecursive($value);
	            }
	            if ($value instanceof stdClass) {
	                $array[$key] = self::arrayCastRecursive((array)$value);
	            }
	        }
	    }
	    if ($array instanceof stdClass) {
	        return self::arrayCastRecursive((array)$array);
	    }
	    return $array;
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

    /**
     * Reemplaza imÃƒÆ’Ã‚Â¡genes inexistentes
     * en arreglos de registros
     * por placeholders.
     *
     * @param array $array
     * @param string $tipo
     * @param string $nombreCampoImagen
     * @return array
     */
    function set_placeholder($array,$nombreCampoImagen="imagen_archivo",$placeholder="user_avatar_preview.png") {
        foreach($array as $keys => $registros){
            if(isset($registros[$nombreCampoImagen])&&!file_exists(base_url("public/uploads/". $registros[$nombreCampoImagen]))){
                $array[$keys]=array_merge($array[$keys],array($nombreCampoImagen=>"assets/images/".$placeholder));
            }
            else{
                $array[$keys]=array_merge($array[$keys],array($nombreCampoImagen=>base_url("assets/images/".$placeholder))); //TODO mejorar este if
            }
        }
        return $array;
    }


    /**
     * Corta Oraciones hasta cierta cantidad de letras
     *
     *
     * @param string $string
     * @param int $your_desired_width
     * @return string $string
     */
    static function tokenTruncate($string, $your_desired_width) {
    	$parts = preg_split('/([\s\n\r]+)/', $string, null, PREG_SPLIT_DELIM_CAPTURE);
    	$parts_count = count($parts);

    	$length = 0;
    	$last_part = 0;
    	for (; $last_part < $parts_count; ++$last_part) {
    		$length += strlen($parts[$last_part]);
    		if ($length > $your_desired_width) { break; }
    	}

    	return implode(array_slice($parts, 0, $last_part));
    }

    /**
     * Convierte las cadenas en tipo UTF8
     * Necesario para usar json_encode
     *
     * @param mixed $d
     * @return mixed
     */
    function utf8ize($d) {
        if (is_array($d)) {
            foreach ($d as $k => $v) {
                $d[$k] = Util::utf8ize($v);
            }
        } else if (is_string ($d)) {
            return utf8_encode($d);
        }
        return $d;
    }

    /**
     * Traducir dia de la semana
     * y mes de una fecha a espanol.
     *
     * @param string $date
     * @return mixed
     */
   static	function translate_date_to_spanish($date=NULL) {
   		$days_of_week_en = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
   		$days_of_week_es = array('Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo');

   		$months_en = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
   		$months_es = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

   		//Traducir Dia de la Semana
   		$date = str_replace($days_of_week_en, $days_of_week_es, $date);

   		//Traducir Mes
   		return str_replace($months_en, $months_es, $date);
   	}

    /**
     * Muestra la fecha en formato de "hace un tiempo"
     *
     * @param string $datetime
     * @return string
     */
     static function timeago($datetime,$ver_minutos='') {

    	if(empty($datetime) || trim($datetime) == "") {
    		return "No date provided";
    	}

    	$datetime1 = new DateTime(date('Y-m-d H:i:s'));
    	$datetime2 = new DateTime($datetime);
    	$interval = $datetime1->diff($datetime2);

    	$suffix = ( $interval->invert ? 'hace ' : '' );
    	if ( $interval->y >= 1 ){
     		if($interval->m >= 1){
    			return $suffix . $interval->y ." ". plural('a&ntilde;o' ) . ($interval->m > 1 ? ", ". $interval->m . " meses" : ($interval->m == 1 ? ", ". $interval->m . " mes" : ""));
    		}
    		if ($interval->d >= 1){
    			return $suffix . $interval->y ." ". plural('a&ntilde;o' ) . ($interval->d > 1 ? ", ". $interval->d . " dias" : ($interval->d == 1 ? ", ". $interval->d . " dia" : "") );
    		}
    	}
    	if ( $interval->m >= 1 ){
     		$remainingDays = ($interval->days <= 61 ? ($interval->days - date("t", strtotime(date('Y-m') . "-01"))) : 0);
    		return $suffix . $interval->m ." ". plural(' meses' ) . ($remainingDays > 1 ? ", $remainingDays dias" : ($remainingDays == 1 ? ", $remainingDays dia" : ""));
    	}
    	if ( $interval->d >= 1 ){
    		if( $interval->d > 1)
    			return $suffix . $interval->d ." ". plural('dia') . ($interval->h > 1 ? ", ". $interval->h ." horas" : ($interval->h == 1 ? ", ". $interval->h ." hora" : ""));
    		else
    			return $suffix . $interval->d ." dia". ($interval->h > 1 ? ", ". $interval->h ." horas" : ($interval->h == 1 ? ", ". $interval->h ." hora" : ""));
    	}
    	if ( $interval->h >= 1 ){
				if(empty($ver_minutos)){
     		return $suffix . $interval->h ." ". plural('hora') . ($interval->i > 1 ? ", ". $interval->i ." minutos" : ($interval->i == 1 ? ", ". $interval->i ." minuto" : ""));
			}else{
				return $suffix . $interval->h ." ". plural('hora');
			}
    	}
    	if ( $interval->i >= 1 ) return $suffix . $interval->i ." ". plural('minuto');
    	return $suffix . $interval->s ." ". plural('segundo');
    }

    /**
     * Retorna un string human readable de una fecha
     *
     * @return string $ts
     */
    static function time2str($ts) {
    	if(!ctype_digit($ts))
    		$ts = strtotime($ts);

    	$diff = time() - $ts;
    	if($diff == 0)
    		return 'now';
    	elseif($diff > 0)
    	{
    		$day_diff = floor($diff / 86400);
    		if($day_diff == 0)
    		{
    			if($diff < 60) return 'Justo ahora';
    			if($diff < 120) return 'Hace un minuto';
    			if($diff < 3600) return 'Hace '.floor($diff / 60) . ' minutos';
    			if($diff < 7200) return 'Hace una hora';
    			if($diff < 86400) return 'Hace '.floor($diff / 3600) . ' horas';
    		}
    		if($day_diff == 1) return 'Ayer';
    		if($day_diff < 7) return 'Hace '.$day_diff . ' d&iacute;as';
    		if($day_diff < 31) return 'Hace '.ceil($day_diff / 7) . ' semanas';
    		if($day_diff < 60) return 'El mes pasado';
    		return date('F Y', $ts);
    	}
    	else
    	{
    		$diff = abs($diff);
    		$day_diff = floor($diff / 86400);
    		if($day_diff == 0)
    		{
    			if($diff < 120) return 'En un minuto';
    			if($diff < 3600) return 'En ' . floor($diff / 60) . ' minutos';
    			if($diff < 7200) return 'En una hora';
    			if($diff < 86400) return 'En ' . floor($diff / 3600) . ' horas';
    		}
    		if($day_diff == 1) return 'MaÃƒÂ±ana';
    		if($day_diff < 4) return date('l', $ts);
    		if($day_diff < 7 + (7 - date('w'))) return 'La prÃƒÂ³xima semana';
    		if(ceil($day_diff / 7) < 4) return 'En ' . ceil($day_diff / 7) . ' semanas';
    		if(date('n', $ts) == date('n') + 1) return 'El prÃƒÂ³ximo mes';
    		return date('F Y', $ts);
    	}
    }

	/*
	 * function para div spinner para ajax
	 */
    static function divAjaxSpinner() {
    	return '<div class="sk-spinner sk-spinner-wave"><div class="sk-rect1"></div><div class="sk-rect2"></div><div class="sk-rect3"></div><div class="sk-rect4"></div><div class="sk-rect5"></div></div>';
    }

    static function actulizarArchivosDocumento($modulos) {
    	$modal ='';
    	$modal.= '<!-- inicia #crearDocumentoModal -->
<div class="modal fade" id="ActualizarDocumentoModal" tabindex="-1" role="dialog" aria-labelledby="ActualizarDocumentoModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                </button>
               <h4 class="modal-title" id="myModalNombreArchivo">Actualizar Archivo</h4>
            </div>
            <div class="modal-body">
            	<table id="documentoTable"  class="table table-hover tabla-dinamica">
                                <thead>
                                 <tr id="documento0">
                                    <td>Relacionado a:</td>
                                    <td>Nombre</td>
                                </thead>
                                <tbody>
                                <tr id="documento0">
                                    <td>
                                     <select id="id_modulo"  name="documento[0][id_modulo]"  class="form-control">
										<option value="">Seleccione</option>';
                                        if(!empty( $modulos ))
                                        {
                                            foreach ($modulos AS $modulo) {
                                                $modal.= '<option value="'.$modulo["id"].'">'.$modulo["nombre"].'</option>';
                                            }
                                        }
                                  $modal.= '</select>
                                    </td><td>
                                     <select id="id_nombre"  name="documento[0][id_nombre]"  class="form-control">
										<option value="">Seleccione</option>
                                     </select>
                                    </td>
                                </tr>
                                </tbody></table>
				 <input id="input-dim-actualizar" type="file" multiple class="file-loading" name="file_data">
            </div><div class="modal-footer"></div></div></div></div>
<!-- termina #crearDocumentoModal -->';
   		return $modal;
    }

   	static function truncate($str, $chars= 20, $end = '...') {
    	if (strlen($str) <= $chars) return $str;
    	$new = substr($str, 0, $chars + 1);
    	return $new .$end;
    }

	function limpiar_cadena_texto($String) {
    	$String = str_replace(array('ÃƒÂ¡','ÃƒÂ ','ÃƒÂ¢','ÃƒÂ£','Ã‚Âª','ÃƒÂ¤'),"a",$String);
    	$String = str_replace(array('Ãƒï¿½','Ãƒâ‚¬','Ãƒâ€š','ÃƒÆ’','Ãƒâ€ž'),"A",$String);
    	$String = str_replace(array('Ãƒï¿½','ÃƒÅ’','ÃƒÅ½','Ãƒï¿½'),"I",$String);
    	$String = str_replace(array('ÃƒÂ­','ÃƒÂ¬','ÃƒÂ®','ÃƒÂ¯'),"i",$String);
    	$String = str_replace(array('ÃƒÂ©','ÃƒÂ¨','ÃƒÂª','ÃƒÂ«'),"e",$String);
    	$String = str_replace(array('Ãƒâ€°','ÃƒË†','ÃƒÅ ','Ãƒâ€¹'),"E",$String);
    	$String = str_replace(array('ÃƒÂ³','ÃƒÂ²','ÃƒÂ´','ÃƒÂµ','ÃƒÂ¶','Ã‚Âº'),"o",$String);
    	$String = str_replace(array('Ãƒâ€œ','Ãƒâ€™','Ãƒâ€�','Ãƒâ€¢','Ãƒâ€“'),"O",$String);
    	$String = str_replace(array('ÃƒÂº','ÃƒÂ¹','ÃƒÂ»','ÃƒÂ¼'),"u",$String);
    	$String = str_replace(array('ÃƒÅ¡','Ãƒâ„¢','Ãƒâ€º','ÃƒÅ“'),"U",$String);
    	$String = str_replace(array('[','^','Ã‚Â´','`','Ã‚Â¨','~',']'),"",$String);
    	$String = str_replace("ÃƒÂ§","c",$String);
    	$String = str_replace("Ãƒâ€¡","C",$String);
    	$String = str_replace("ÃƒÂ±","n",$String);
    	$String = str_replace("Ãƒâ€˜","N",$String);
    	$String = str_replace("Ãƒï¿½","Y",$String);
    	$String = str_replace("ÃƒÂ½","y",$String);

    	$String = str_replace("&aacute;","a",$String);
    	$String = str_replace("&Aacute;","A",$String);
    	$String = str_replace("&eacute;","e",$String);
    	$String = str_replace("&Eacute;","E",$String);
    	$String = str_replace("&iacute;","i",$String);
    	$String = str_replace("&Iacute;","I",$String);
    	$String = str_replace("&oacute;","o",$String);
    	$String = str_replace("&Oacute;","O",$String);
    	$String = str_replace("&uacute;","u",$String);
    	$String = str_replace("&Uacute;","U",$String);
    	$String = str_replace("&nbsp;"," ",$String);
    	return $String;
    }

public static function  sanear_string($string)
{

    $string = trim($string);

    $string = str_replace(
        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
        array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
        $string
    );

    $string = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
        $string
    );

    $string = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
        $string
    );

    $string = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
        array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
        $string
    );

    $string = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
        $string
    );

    $string = str_replace(
        array('ñ', 'Ñ', 'ç', 'Ç'),
        array('n', 'N', 'c', 'C',),
        $string
    );

    //Esta parte se encarga de eliminar cualquier caracter extraño  "\", 
    $string = str_replace(
        array("¨", "º", "-", "~",
             "#", "@", "|", "!", "\"",
             "·", "$", "%", "&", "/",
             "(", ")", "?", "'", "¡",
             "¿", "[", "^", "<code>", "]",
             "+", "}", "{", "¨", "´",
             ">", "< ", ";", ",", ":",
             ".", " "),
        '',
        $string
    );


    return $string;
}

    /**
     * Ordena un arreglo multidimensional de acuerdo al valor de un campo
     *
     * @param array $array
     * @return string $key
     */
    static function aasort (&$array, $key) {
        $sorter=array();
        $ret=array();
        reset($array);
        foreach ($array as $ii => $va) {
            $sorter[$ii]=$va[$key];
        }
        asort($sorter);
        foreach ($sorter as $ii => $va) {
            $ret[$ii]=$array[$ii];
        }
        $array=$ret;
    }

	public static function generate_ramdom_token() {
		return md5(uniqid(mt_rand(), TRUE));
	}

	/**
	 * Armar el fieldset que proviene del POST
	 * de formularios de creacion/edicion.
	 *
	 * return array
	 */
	public static function set_fieldset($findice=NULL, $sindex=NULL, $date_format="") {
		if(empty($_POST) || $findice==NULL){
			return false;
		}

	if(is_numeric($sindex)){
		$POST = $_POST[$findice][$sindex];
	}elseif(!empty($findice)){
		$POST = $_POST[$findice];

	}else{
		$POST = $_POST;
	}
		//$POST = is_numeric($sindex) ? $_POST[$findice][$sindex] : $findice == true ? $_POST : $_POST[$findice];

		//Recorrer arreglo e insertar los valores que no estan vacios
		//en el fieldset
		foreach ($POST AS $fieldname => $fieldvalue) {
			if(empty($fieldvalue)){
				continue;
			}

			//check if is an array
			if(is_array($fieldvalue)){
				foreach ($fieldvalue AS $name => $value) {
					if($value != ""){

						$value = self::$ci->security->xss_clean($value);

						if(preg_match("/fecha/i", $name)){

							//reemplazar slahsh por guion
							$value = str_replace('/', '-', $value);

							//Darle mformato a la fecha
							$fieldset[$name] = date("Y-m-d", strtotime($value));
						}
						else{
							$fieldset[$name] = self::$ci->security->xss_clean($value);
						}
					}
				}
			}else{

				$fieldvalue = self::$ci->security->xss_clean($fieldvalue);

				if(preg_match("/fecha/i", $fieldname)){

					//reemplazar slahsh por guion
					$fieldvalue = str_replace('/', '-', $fieldvalue);

					//Darle mformato a la fecha
					$fieldset[$fieldname] = date("Y-m-d", strtotime($fieldvalue));
				}
				else{
					$fieldset[$fieldname] = $fieldvalue;
				}
			}
		}

		return $fieldset;
	}

	/**
	 * Esta funcion verifica si el valor de
	 * un arreglo existe y no es vacio.
	 *
	 */
	public static function verificar_valor($valor=NULL) {
		if($valor==NULL){
			return "";
		}

		return !empty($valor) ? $valor : "";
	}

	public static function generar_codigo($letra='',$numero=0, $len_numero=6, $inicia='0') {
   $codigo = str_pad($numero,$len_numero,$inicia, STR_PAD_LEFT);
	 return $letra.$codigo;
	}

	public static function zerofill($valor=NULL, $longitud=NULL) {

		$respuesta  = str_pad($valor, $longitud, '0', STR_PAD_LEFT);
 		return !empty($respuesta) ? $respuesta : "0";
 	}
}
