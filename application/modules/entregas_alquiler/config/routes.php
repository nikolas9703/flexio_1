<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['entregas_alquiler/crear'] = 'entregas_alquiler/crear';
$route['entregas_alquiler/editar/(:any)'] = 'entregas_alquiler/editar/$1';
$route['entregas_alquiler/listar'] = 'entregas_alquiler/listar';

//peticiones ajax
$route['entregas_alquiler/ajax-listar'] = 'entregas_alquiler/ajax_listar';
$route['entregas_alquiler/ajax-exportar'] = 'entregas_alquiler/ajax_exportar';
$route['entregas_alquiler/ajax-get-serie-ubicacion'] = 'entregas_alquiler/ajax_get_serie_ubicacion';
$route['entregas_alquiler/ajax-guardar-comentario'] = 'entregas_alquiler/ajax_guardar_comentario';
