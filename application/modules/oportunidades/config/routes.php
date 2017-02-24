<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['oportunidades/listar'] = 'oportunidades/listar';
$route['oportunidades/crear'] = 'oportunidades/crear';
$route['oportunidades/editar/(:any)'] = 'oportunidades/editar/$1';

//peticiones ajax
$route['oportunidades/ajax-listar'] = 'oportunidades/ajax_listar';
$route['oportunidades/ajax-exportar'] = 'oportunidades/ajax_exportar';
$route['oportunidades/ajax-change-status'] = 'oportunidades/ajax_change_status';
$route['oportunidades/ajax-guardar-comentario'] = 'oportunidades/ajax_guardar_comentario';
$route['oportunidades/ajax-asociar-cotizacion'] = 'oportunidades/ajax_asociar_cotizacion';
