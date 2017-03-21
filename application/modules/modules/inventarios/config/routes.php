<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$route['inventarios/listar']        = 'inventarios/listar';
$route['inventarios/ocultotabla']   = 'inventarios/ocultotabla';

//Ajax
$route['inventarios/ajax-exportar']                 = 'inventarios/ajax_exportar';
$route['inventarios/ajax-listar']                   = 'inventarios/ajax_listar';
$route['inventarios/ajax-listar-series']            = 'inventarios/ajax_listar_series';
$route['inventarios/ajax-get-item-unidad']          = 'inventarios/ajax_get_item_unidad';
$route['inventarios/ajax-get-existencia']           = 'inventarios/ajax_get_existencia';
$route['inventarios/ajax-delete-item-unidad']       = 'inventarios/ajax_delete_item_unidad';
$route['inventarios/ajax-listar-en-inventario']     = 'inventarios/ajax_listar_en_inventario';
$route['inventarios/ajax-listar-historial-ajustes'] = 'inventarios/ajax_listar_historial_ajustes';
$route['inventarios/ajax-listar-bitacora-traslados']= 'inventarios/ajax_listar_bitacora_traslados';
$route['inventarios/ajax-get-items'] = 'inventarios/ajax_get_items';
$route['inventarios/ajax-get-items-categoria'] = 'inventarios/ajax_get_items_categoria';
$route['inventarios/ajax-get-cantidad'] = 'inventarios/ajax_get_cantidad';
$route['inventarios/ajax-get-precios'] = 'inventarios/ajax_get_precios';
$route['inventarios/ajax-guardar-documentos'] = 'inventarios/ajax_guardar_documentos';
$route['inventarios/ajax-get-typehead-items'] = 'inventarios/ajax_get_typehead_items';
$route['inventarios/ajax-get-codigo-validez'] = 'inventarios/ajax_get_codigo_validez';
$route['inventarios/ajax-quick-add'] = 'inventarios/ajax_quick_add';


//Formulario crear/editar
$route['inventarios/crear']         = 'inventarios/crear';
$route['inventarios/ver/(:any)']    = 'inventarios/editar/$1';
