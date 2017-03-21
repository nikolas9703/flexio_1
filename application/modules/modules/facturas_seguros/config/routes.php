<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['facturas_seguros/listar'] = 'facturas_seguros/listar';
$route['facturas_seguros/ver/(:any)'] = 'facturas_seguros/ver/$1';
$route['facturas_seguros/crear'] = 'facturas_seguros/crear';
$route['facturas_seguros/editar/(:any)'] = 'facturas_seguros/editar/$1';
//$route['facturas_seguros/guardar'] = 'facturas_seguros/guardar';

$route['facturas_seguros/ajax-listar'] = 'facturas_seguros/ajax_listar';
$route['facturas_seguros/ajax-listar-de-item'] = 'facturas_seguros/ajax_listar_de_item';
$route['facturas_seguros/ajax-factura-info'] = 'facturas_seguros/ajax_factura_info';
$route['facturas_seguros/ajax-empezar-factura-desde'] = 'facturas_seguros/ajax_empezar_factura_desde';
$route['facturas_seguros/ajax-getAll'] = 'facturas_seguros/ajax_getAll';
$route['facturas_seguros/ajax-getfacturas_segurosDevoluciones'] = 'facturas_seguros/ajax_getfacturas_segurosDevoluciones';
$route['facturas_seguros/ajax-getFacturadoCompleto'] = 'facturas_seguros/ajax_getFacturadoCompleto';
$route['facturas_seguros/ajax-getFacturadoValido'] = 'facturas_seguros/ajax_getFacturadoValidos';
//$route['facturas_seguros/exportar'] = 'facturas_seguros/exportar';
$route['facturas_seguros/ajax-guardar-documentos'] = 'facturas_seguros/ajax_guardar_documentos';
$route['facturas_seguros/ajax-guardar-comentario'] = 'facturas_seguros/ajax_guardar_comentario';
$route['facturas_seguros/ajax-seleccionar-items'] = 'facturas_seguros/ajax_seleccionar_items';
$route['facturas_seguros/ajax-cliente-info'] = 'facturas_seguros/ajax_cliente_info';

//empezables
$route['facturas_seguros/ajax_catalogo_ordenes_ventas'] = 'facturas_seguros/ajax_catalogo_ordenes_ventas';
$route['facturas_seguros/ajax_catalogo_ordenes_trabajo'] = 'facturas_seguros/ajax_catalogo_ordenes_trabajo';
$route['facturas_seguros/ajax_catalogo_contrato_ventas'] = 'facturas_seguros/ajax_catalogo_contrato_ventas';
$route['facturas_seguros/ajax_catalogo_contrato_alquiler'] = 'facturas_seguros/ajax_catalogo_contrato_alquiler';
