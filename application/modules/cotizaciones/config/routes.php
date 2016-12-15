<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['cotizaciones/listar'] = 'cotizaciones/listar';
$route['cotizaciones/ajax-listar'] = 'cotizaciones/ajax_listar';
$route['cotizaciones/ajax-listar2'] = 'cotizaciones/ajax_listar2';
$route['cotizaciones/crear'] = 'cotizaciones/crear';
$route['cotizaciones/ver/(:any)'] = 'cotizaciones/ver/$1';
$route['cotizaciones/ocultoformulariocomentarios'] = 'cotizaciones/ocultoformulariocomentarios';
$route['cotizaciones/guardar'] = 'cotizaciones/guardar';
$route['cotizaciones/ajax-cliente-info'] = 'cotizaciones/ajax_cliente_info';
$route['cotizaciones/ajax-items-cotizacion'] = 'cotizaciones/ajax_items_cotizacion';
$route['cotizaciones/ajax-cotizacion-info'] = 'cotizaciones/ajax_cotizacion_info';
$route['cotizaciones/convertir-order-venta/(:any)'] = 'cotizaciones/convertir_order_venta/$1';
$route['cotizaciones/guardarOrdenVenta'] = 'cotizaciones/guardarOrdenVenta';
$route['cotizaciones/ajax-data-formulario'] = 'cotizaciones/ajax_data_formulario';
$route['cotizaciones/ajax-data-formulario-alquiler'] = 'cotizaciones/ajax_data_formulario_alquiler';
$route['cotizaciones/ajax-guardar-comentario'] = 'cotizaciones/ajax_guardar_comentario';
$route['cotizaciones/ajax-guardar-documentos'] = 'cotizaciones/ajax_guardar_documentos';



/* End of file routes.php */
/* Location: ./application/config/routes.php */
