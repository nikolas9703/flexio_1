<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['ordenes_ventas/listar'] = 'ordenes_ventas/listar';
$route['ordenes_ventas/crear'] = 'ordenes_ventas/crear';
$route['ordenes_ventas/ver/(:any)'] = 'ordenes_ventas/ver/$1';
$route['ordenes_ventas/ajax-listar'] = 'ordenes_ventas/ajax_listar';
$route['ordenes_ventas/ajax-ordenVenta-info'] = 'ordenes_ventas/ajax_ordenVenta_info';
$route['ordenes_ventas/ajax-seleccionar-orden-venta'] = 'ordenes_ventas/ajax_ordenVenta_info';
$route['ordenes_ventas/facturar/(:any)'] = 'ordenes_ventas/facturar/$1';
$route['ordenes_ventas/ajax-get-item-existencia'] = 'ordenes_ventas/ajax_get_item_existencia';
$route['ordenes_ventas/ocultoformulariocomentarios'] = 'ordenes_ventas/ocultoformulariocomentarios';
$route['ordenes_ventas/ajax-guardar-comentario'] = 'ordenes_ventas/ajax_guardar_comentario';
$route['ordenes_ventas/ajax-guardar-documentos'] = 'ordenes_ventas/ajax_guardar_documentos';