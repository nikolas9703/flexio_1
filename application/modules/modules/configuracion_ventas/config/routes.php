<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$route['configuracion_ventas/listar/(:any)']           = 'configuracion_ventas/listar/$1';
//Ajax
$route['configuracion_ventas/ajax-exportar']                    = 'configuracion_ventas/ajax_exportar';
//Ajax categorías clientes.
$route['configuracion_ventas/ajax-listar-categorias']           = 'configuracion_ventas/ajax_listar_categorias';
$route['configuracion_ventas/ajax-guardar-categorias']          = 'configuracion_ventas/ajax_guardar_categorias';
$route['configuracion_ventas/ajax-get-categoria']               = 'configuracion_ventas/ajax_get_categoria';
$route['configuracion_ventas/ajax-cambiar-estado-categoria']    = 'configuracion_ventas/ajax_cambiar_estado_categoria';
//Ajax tipos clientes.
$route['configuracion_ventas/ajax-listar-tipos']                = 'configuracion_ventas/ajax_listar_tipos';
$route['configuracion_ventas/ajax-guardar-tipos']               = 'configuracion_ventas/ajax_guardar_tipos';
$route['configuracion_ventas/ajax-get-tipos']                   = 'configuracion_ventas/ajax_get_tipos';
$route['configuracion_ventas/ajax-cambiar-estado-tipos']        = 'configuracion_ventas/ajax_cambiar_estado_tipos';