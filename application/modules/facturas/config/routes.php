<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['facturas/listar'] = 'facturas/listar';
//$route['facturas/crear'] = 'facturas/crear2';
$route['facturas/crear'] = 'facturas/creando';
$route['facturas/guardar'] = 'facturas/guardar';
$route['facturas/ver/(:any)'] = 'facturas/ver/$1';
$route['facturas/ajax-listar'] = 'facturas/ajax_listar';
$route['facturas/ajax-listar-de-item'] = 'facturas/ajax_listar_de_item';
$route['facturas/ajax-factura-info'] = 'facturas/ajax_factura_info';
$route['facturas/ajax-empezar-factura-desde'] = 'facturas/ajax_empezar_factura_desde';
$route['facturas/ajax-getAll'] = 'facturas/ajax_getAll';
$route['facturas/ajax-getFacturasDevoluciones'] = 'facturas/ajax_getFacturasDevoluciones';
$route['facturas/ajax-getFacturadoCompleto'] = 'facturas/ajax_getFacturadoCompleto';
$route['facturas/ajax-getFacturadoValido'] = 'facturas/ajax_getFacturadoValidos';
$route['facturas/exportar'] = 'facturas/exportar';
$route['facturas/ajax-guardar-documentos'] = 'facturas/ajax_guardar_documentos';
$route['facturas/crear2'] = 'facturas/crear2';
$route['facturas/ajax-guardar-comentario'] = 'facturas/ajax_guardar_comentario';
$route['facturas/ajax-seleccionar-items'] = 'facturas/ajax_seleccionar_items';
$route['facturas/ajax-cliente-info'] = 'facturas/ajax_cliente_info';

//editar
$route['facturas/editar/(:any)'] = 'facturas/editar/$1';
//empezables
$route['facturas/ajax_catalogo_ordenes_ventas'] = 'facturas/ajax_catalogo_ordenes_ventas';
$route['facturas/ajax_catalogo_ordenes_trabajo'] = 'facturas/ajax_catalogo_ordenes_trabajo';
$route['facturas/ajax_catalogo_contrato_ventas'] = 'facturas/ajax_catalogo_contrato_ventas';
$route['facturas/ajax_catalogo_contrato_alquiler'] = 'facturas/ajax_catalogo_contrato_alquiler';
