<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['devoluciones_alquiler/listar'] = 'devoluciones_alquiler/listar';

//peticiones ajax
$route['devoluciones_alquiler/ajax-listar']                = 'devoluciones_alquiler/ajax_listar';
$route['devoluciones_alquiler/ajax-exportar']              = 'devoluciones_alquiler/ajax_exportar';
$route['devoluciones_alquiler/ajax-seleccionar-items-entrega'] = 'devoluciones_alquiler/ajax_seleccionar_items_entrega';
$route['devoluciones_alquiler/ajax-seleccionar-items-contrato'] = 'devoluciones_alquiler/ajax_seleccionar_items_contrato';
$route['devoluciones_alquiler/ajax-seleccionar-entrega-info'] = 'devoluciones_alquiler/ajax_seleccionar_entrega_info';
$route['devoluciones_alquiler/ajax-seleccionar-info'] = 'devoluciones_alquiler/ajax_seleccionar_info';
$route['devoluciones_alquiler/ajax-guardar-retorno'] = 'devoluciones_alquiler/ajax_guardar_retorno';
$route['devoluciones_alquiler/ajax-guardar-comentario'] = 'devoluciones_alquiler/ajax_guardar_comentario';

//Peticiones formularios
//$route['devoluciones_alquiler/crear'] = 'devoluciones_alquiler/crear';
$route['devoluciones_alquiler/editar/(:any)'] = 'devoluciones_alquiler/editar/$1';
 
