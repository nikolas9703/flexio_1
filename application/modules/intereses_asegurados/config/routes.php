<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$route['intereses_asegurados/listar']        = 'intereses_asegurados/listar';
$route['intereses_asegurados/ocultotabla']   = 'intereses_asegurados/ocultotabla';

//Ajax 
$route['intereses_asegurados/ajax-listar']        = 'intereses_asegurados/ajax_listar';
$route['intereses_asegurados/ajax-check-persona']        = 'intereses_asegurados/ajax_check_persona';
$route['intereses_asegurados/ajax-check-vehiculo']        = 'intereses_asegurados/ajax_check_vehiculo';
$route['intereses_asegurados/ajax-check-aereo']        = 'intereses_asegurados/ajax_check_aereo';
$route['intereses_asegurados/ajax-check-maritimo']        = 'intereses_asegurados/ajax_check_maritimo';
$route['intereses_asegurados/ajax-check-ubicacion']        = 'intereses_asegurados/ajax_check_ubicacion';
$route['intereses_asegurados/ajax-check-proyecto']        = 'intereses_asegurados/ajax_check_proyecto';
$route['intereses_asegurados/ajax-check-carga']        = 'intereses_asegurados/ajax_check_carga';
$route['intereses_asegurados/ajax-get-interes-asegurado']  = 'intereses_asegurados/ajax_get_interes_asegurado';
$route['intereses_asegurados/ajax-guardar-documentos']  = 'intereses_asegurados/ajax_guardar_documentos';

//Formulario crear/editar
$route['intereses_asegurados/crear']         = 'intereses_asegurados/crear';
$route['intereses_asegurados/ver/(:any)']    = 'intereses_asegurados/editar/$1';
$route['intereses_asegurados/editar/(:any)']    = 'intereses_asegurados/editar/$1';
$route['intereses_asegurados/eliminar/(:any)']    = 'intereses_asegurados/eliminar';

