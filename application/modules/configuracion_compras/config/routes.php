<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$route['configuracion_compras/listar']           = 'configuracion_compras/listar';
$route['configuracion_compras/ocultotabla']      = 'configuracion_compras/ocultotabla';

//Ajax
$route['configuracion_compras/ajax-exportar']                    = 'configuracion_compras/ajax_exportar';
$route['configuracion_compras/ajax-listar-chequeras']            = 'configuracion_compras/ajax_listar_chequeras';
$route['configuracion_compras/ajax-cambiar-estado-chequera']     = 'configuracion_compras/ajax_cambiar_estado_chequera';
$route['configuracion_compras/ajax-guardar']                     = 'configuracion_compras/ajax_guardar';
$route['configuracion_compras/ajax-get-chequera']                = 'configuracion_compras/ajax_get_chequera';
//Ajax categorías proveedor.
$route['configuracion_compras/ajax-listar-categorias']           = 'configuracion_compras/ajax_listar_categorias';
$route['configuracion_compras/ajax-guardar-categorias']          = 'configuracion_compras/ajax_guardar_categorias';
$route['configuracion_compras/ajax-get-categoria']               = 'configuracion_compras/ajax_get_categoria';
$route['configuracion_compras/ajax-cambiar-estado-categoria']    = 'configuracion_compras/ajax_cambiar_estado_categoria';
//Ajax tipos proveedor.
$route['configuracion_compras/ajax-listar-tipos']           = 'configuracion_compras/ajax_listar_tipos';
$route['configuracion_compras/ajax-guardar-tipos']          = 'configuracion_compras/ajax_guardar_tipos';
$route['configuracion_compras/ajax-get-tipos']               = 'configuracion_compras/ajax_get_tipos';
$route['configuracion_compras/ajax-cambiar-estado-tipos']    = 'configuracion_compras/ajax_cambiar_estado_tipos';