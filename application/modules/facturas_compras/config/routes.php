<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['facturas_compras/listar']               = 'facturas_compras/listar';
$route['facturas_compras/crear']                = 'facturas_compras/crear';
$route['facturas_compras/ver/(:any)']           = 'facturas_compras/ver/$1';
$route['facturas_compras/ajax-listar']          = 'facturas_compras/ajax_listar';
$route['facturas_compras/ajax-exportar']        = 'facturas_compras/ajax_exportar';
$route['facturas_compras/ajax-listar-de-item']  = 'facturas_compras/ajax_listar_de_item';

//ajax - formulario de creacion/edicion de facturas
$route['facturas_compras/ajax-get-items']           = 'facturas_compras/ajax_get_items';
$route['facturas_compras/ajax-get-empezar-desde']   = 'facturas_compras/ajax_get_empezar_desde';
$route['facturas_compras/ajax-get-factura']         = 'facturas_compras/ajax_get_factura';
$route['facturas_compras/ajax-get-factura-all']         = 'facturas_compras/ajax_getFacturadoCompleto';
$route['facturas_compras/ajax-factura-info']        = 'facturas_compras/ajax_factura_info';
$route['facturas_compras/ajax-guardar-comentario']        = 'facturas_compras/ajax_guardar_comentario';
$route['facturas_compras/ajax-guardar-documentos'] = 'facturas_compras/ajax_guardar_documentos';
$route['facturas_compras/ajax-get-empezable'] = 'facturas_compras/ajax_get_empezable';
$route['facturas_compras/ajax-get-ordenes-items'] = 'facturas_compras/ajax_get_ordenes_items';


//No se coloca la ruta "Guardar" porque esta no tiene vista
