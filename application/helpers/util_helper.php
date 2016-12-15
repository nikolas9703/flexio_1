<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

if(!function_exists('session_isset'))
{
	/**
	 *Check if session already exist
	 */
	function session_isset(){
		return get_instance()->session->userdata('id_usuario');
	}
}
/**
 * @params $nombre es el nombre del archivo
 * @return la clase para ser usado en la etiqueda i
 */
if(!function_exists('file_ico'))
{
	function file_ico($tipo){
		$nombre = $tipo;
		$extension = 'fa fa-file';
		switch($nombre){

			case (preg_match('/.doc|.docx/', $nombre) ? true : false):
				$extension ='fa fa-file-word-o';
			break;
			case (preg_match('/.pdf/', $nombre) ? true : false):
				$extension ='fa fa-file-pdf-o';
			break;
			case (preg_match('/.xls|.xlsx/', $nombre) ? true : false):
				$extension ='fa fa-file-excel-o';
			break;
			case (preg_match('/.ppt|.pptx/', $nombre) ? true : false):
				$extension ='fa fa-file-powerpoint-o';
		    break;
		    case (preg_match('/.gif|.png|.jpg|.jpeg/', $nombre) ? true : false):
		    	$extension ='fa fa-file-image-o';
		    break;
		    case (preg_match('/.zip|.rar|.gz|.7z/', $nombre) ? true : false):
		    	$extension ='fa fa-file-zip-o';
		    break;
		}

		return $extension;
	}
}
if(!function_exists('parse_fecha'))
{
	function parse_fecha($fecha){
		if(!empty($fecha))return date("d/m/Y", strtotime($fecha));
	}
}
if(!function_exists('parse_hora'))
{
	function parse_hora($fecha){
		if(!empty($fecha))return date( "h:i:s a", strtotime($fecha));
	}
}
if(!function_exists('getDownloadLink'))
{
	function getDownloadLink($ruta, $nombre_random){
		if(!empty($ruta) && !empty($nombre_random))return $src = base_url($ruta."/".$nombre_random);
	}
}
if(!function_exists('date_options'))
{
	function date_options($fecha_string){
		$fecha = array();
		switch ($fecha_string){
			case 'hoy':
		       $fecha =  array('hoy'=> array(date('Y-m-d',time()), date('Y-m-d',strtotime("yesterday")) ));
		    break;
			case 'ayer':
			   $fecha =  array('ayer'=> array(date('Y-m-d',strtotime("yesterday")),date('Y-m-d',strtotime("-1 yesterday")) ));
			break;
			case 'esta_semana':
				$fecha = array('esta_semana'=> array(date('YW',strtotime("-1 week")), date('YW',strtotime("-2 week"))));
			break;
			case 'ultima_semana':
				$fecha = array('ultima_semana'=> array(date('YW',strtotime("-2 week")), date('YW',strtotime("-3 week"))));
			break;
			case 'este_mes':
				$fecha = array('este_mes'=> array( date('Ym',strtotime("this month")), date('Ym',strtotime("last month")) ));
			break;
			case 'ultimo_mes':
				$fecha = array('ultimo_mes'=> array( date('Ym',strtotime("last month")), date('Ym',strtotime("-2 month")) ));
			break;
			case 'ultimos_7_dias':
			$fecha = array('ultimos_7_dias'=> array( date('Y-m-d',strtotime("-7 days")), date('Y-m-d',strtotime("-14 days")) ));
			break;
			case 'ultimos_30_dias':
			$fecha = array('ultimos_7_dias'=> array( date('Y-m-d',strtotime("-30 days")), date('Y-m-d',strtotime("-60 days")) ));
			break;
			default:
			   $fecha = array('este_mes'=> array( date('Ym',strtotime("this month")), date('Ym',strtotime("last month")) ));
			break;
		}
		return $fecha;
	}
}
?>
