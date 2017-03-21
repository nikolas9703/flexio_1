<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['cotizaciones_alquiler/listar'] = 'cotizaciones_alquiler/listar';
$route['cotizaciones_alquiler/crear'] = 'cotizaciones_alquiler/crear';
$route['cotizaciones_alquiler/editar/(:any)'] = 'cotizaciones_alquiler/editar/$1';

//peticiones ajax
$route['cotizaciones_alquiler/ajax-listar'] = 'cotizaciones_alquiler/ajax_listar';
$route['cotizaciones_alquiler/ajax-exportar'] = 'cotizaciones_alquiler/ajax_exportar';
$route['cotizaciones_alquiler/ajax-get-cotizacion'] = 'cotizaciones_alquiler/ajax_get_cotizacion';
