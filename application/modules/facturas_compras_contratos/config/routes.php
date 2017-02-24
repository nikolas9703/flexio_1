<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['facturas_compras_contratos/listar']               = 'facturas_compras_contratos/listar';
$route['facturas_compras_contratos/crear']                = 'facturas_compras_contratos/crear';
$route['facturas_compras_contratos/ver/(:any)']           = 'facturas_compras_contratos/ver/$1';
$route['facturas_compras_contratos/ajax-listar']          = 'facturas_compras_contratos/ajax_listar';
$route['facturas_compras_contratos/ajax-exportar']        = 'facturas_compras_contratos/ajax_exportar';
$route['facturas_compras_contratos/ajax-listar-de-item']  = 'facturas_compras_contratos/ajax_listar_de_item';

//ajax - formulario de creacion/edicion de facturas
$route['facturas_compras_contratos/ajax-get-items']           = 'facturas_compras_contratos/ajax_get_items';
$route['facturas_compras_contratos/ajax-get-empezar-desde']   = 'facturas_compras_contratos/ajax_get_empezar_desde';
$route['facturas_compras_contratos/ajax-get-factura']         = 'facturas_compras_contratos/ajax_get_factura';
$route['facturas_compras_contratos/ajax-get-factura-all']     = 'facturas_compras_contratos/ajax_getFacturadoCompleto';
$route['facturas_compras_contratos/ajax-factura-info']        = 'facturas_compras_contratos/ajax_factura_info';
$route['facturas_compras_contratos/ajax-guardar-documentos']  = 'facturas_compras_contratos/ajax_guardar_documentos';
//No se coloca la ruta "Guardar" porque esta no tiene vista
