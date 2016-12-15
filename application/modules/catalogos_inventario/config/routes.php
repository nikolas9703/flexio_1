<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$route['catalogos_inventario/listar']           = 'catalogos_inventario/listar';
$route['catalogos_inventario/ocultotabla']      = 'catalogos_inventario/ocultotabla';

//Ajax
$route['catalogos_inventario/ajax-exportar']                    = 'catalogos_inventario/ajax_exportar';
$route['catalogos_inventario/ajax-listar-categorias']           = 'catalogos_inventario/ajax_listar_categorias';
$route['catalogos_inventario/ajax-listar-precios']              = 'catalogos_inventario/ajax_listar_precios';
$route['catalogos_inventario/ajax-listar-unidades']             = 'catalogos_inventario/ajax_listar_unidades';
$route['catalogos_inventario/ajax-listar-razones-ajustes']      = 'catalogos_inventario/ajax_listar_razones_ajustes';
$route['catalogos_inventario/ajax-cambiar-estado-categoria']    = 'catalogos_inventario/ajax_cambiar_estado_categoria';
$route['catalogos_inventario/ajax-cambiar-estado-precio']       = 'catalogos_inventario/ajax_cambiar_estado_precio';
$route['catalogos_inventario/ajax-cambiar-estado-unidad']       = 'catalogos_inventario/ajax_cambiar_estado_unidad';
$route['catalogos_inventario/ajax-cambiar-estado-razon']        = 'catalogos_inventario/ajax_cambiar_estado_razon';
$route['catalogos_inventario/ajax-guardar']                     = 'catalogos_inventario/ajax_guardar';
$route['catalogos_inventario/ajax-guardar-precio']              = 'catalogos_inventario/ajax_guardar_precio';
$route['catalogos_inventario/ajax-guardar-unidad']              = 'catalogos_inventario/ajax_guardar_unidad';
$route['catalogos_inventario/ajax-guardar-razon']               = 'catalogos_inventario/ajax_guardar_razon';
$route['catalogos_inventario/ajax-get-categoria']               = 'catalogos_inventario/ajax_get_categoria';
$route['catalogos_inventario/ajax-get-precio']                  = 'catalogos_inventario/ajax_get_precio';
$route['catalogos_inventario/ajax-get-unidad']                  = 'catalogos_inventario/ajax_get_unidad';
$route['catalogos_inventario/ajax-get-razon']                   = 'catalogos_inventario/ajax_get_razon';
$route['catalogos_inventario/ajax-select-precio']               = 'catalogos_inventario/ajax_select_precio';
