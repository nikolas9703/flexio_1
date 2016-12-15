<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['contratos_alquiler/listar'] = 'contratos_alquiler/listar';
$route['contratos_alquiler/bitacora/(:any)'] = 'contratos_alquiler/bitacora/$1';
 
//peticiones ajax
$route['contratos_alquiler/ajax-listar'] = 'contratos_alquiler/ajax_listar';
$route['contratos_alquiler/ajax-listar2'] = 'contratos_alquiler/ajax_listar2';
$route['contratos_alquiler/ajax-exportar'] = 'contratos_alquiler/ajax_exportar';
$route['contratos_alquiler/ajax-guardar-comentario'] = 'contratos_alquiler/ajax_guardar_comentario';
$route['contratos_alquiler/ajax-guardar-documentos'] = 'contratos_alquiler/ajax_guardar_documentos';
$route['contratos_alquiler/ajax-contrato-info'] = 'contratos_alquiler/ajax_contrato_info';