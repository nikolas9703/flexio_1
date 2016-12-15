<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['ordenes_trabajo/listar'] = 'ordenes_trabajo/listar';
$route['ordenes_trabajo/crear'] = 'ordenes_trabajo/crear';
$route['ordenes_trabajo/ver/(:any)'] = 'ordenes_trabajo/crear/$1';
$route['ordenes_trabajo/ajax-listar'] = 'ordenes_trabajo/ajax_listar';
$route['ordenes_trabajo/ajax-seleccionar-cat-orden-de'] = 'ordenes_trabajo/ajax_seleccionar_cat_orden_de';
$route['ordenes_trabajo/ajax-guardar-orden'] = 'ordenes_trabajo/ajax_guardar_orden';
$route['ordenes_trabajo/ajax-seleccionar-items'] = 'ordenes_trabajo/ajax_seleccionar_items';
$route['ordenes_trabajo/ajax-seleccionar-items-serializados'] = 'ordenes_trabajo/ajax_seleccionar_items_serializados';
$route['ordenes_trabajo/ajax-seleccionar-series-item'] = 'ordenes_trabajo/ajax_seleccionar_series_de_item';
$route['ordenes_trabajo/ajax-seleccionar-items-servicio-por-categoria'] = 'ordenes_trabajo/ajax_seleccionar_items_servicio_por_categoria';
$route['ordenes_trabajo/ajax-seleccionar-items-utilizados-por-categoria'] = 'ordenes_trabajo/ajax_seleccionar_items_utilizados_por_categoria';
$route['ordenes_trabajo/ajax-seleccionar-unidades-item'] = 'ordenes_trabajo/ajax_seleccionar_unidades_item';
$route['ordenes_trabajo/ajax-eliminar-item'] = 'ordenes_trabajo/ajax_eliminar_item';
$route['ordenes_trabajo/ajax-eliminar-servicio'] = 'ordenes_trabajo/ajax_eliminar_servicio';
$route['ordenes_trabajo/ajax-seleccionar-orden'] = 'ordenes_trabajo/ajax_seleccionar_orden';
$route['ordenes_trabajo/ajax-get-equipotrabajo-info'] = 'ordenes_trabajo/ajax_get_equipotrabajo_info';
$route['ordenes_trabajo/ajax-guardar-comentario'] = 'ordenes_trabajo/ajax_guardar_comentario';
