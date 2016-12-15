<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['ordenes_alquiler/listar'] = 'ordenes_alquiler/listar';
$route['ordenes_alquiler/crear'] = 'ordenes_alquiler/crear';
$route['ordenes_alquiler/crear/(:any)/(:num)'] = 'ordenes_alquiler/crear/$1/$2';
$route['ordenes_alquiler/ver/(:any)'] = 'ordenes_alquiler/ver/$1';
$route['ordenes_alquiler/ajax-listar'] = 'ordenes_alquiler/ajax_listar';
$route['ordenes_alquiler/ajax-ordenVenta-info'] = 'ordenes_alquiler/ajax_ordenVenta_info';
$route['ordenes_alquiler/ajax-seleccionar-orden-venta'] = 'ordenes_alquiler/ajax_ordenVenta_info';
$route['ordenes_alquiler/facturar/(:any)'] = 'ordenes_alquiler/facturar/$1';
$route['ordenes_alquiler/ajax-get-item-existencia'] = 'ordenes_alquiler/ajax_get_item_existencia';
$route['ordenes_alquiler/ocultoformulariocomentarios'] = 'ordenes_alquiler/ocultoformulariocomentarios';
$route['ordenes_alquiler/ajax-guardar-comentario'] = 'ordenes_alquiler/ajax_guardar_comentario';
$route['ordenes_alquiler/ajax-guardar-documentos'] = 'ordenes_alquiler/ajax_guardar_documentos';
