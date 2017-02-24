<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['cobros_seguros/listar'] = 'cobros_seguros/listar';
$route['cobros_seguros/crear'] = 'cobros_seguros/crear';
$route['cobros_seguros/ver/(:any)'] = 'cobros_seguros/ver/$1';
$route['cobros_seguros/registrar-pago/(:any)'] = 'cobros_seguros/registrar_pago/$1';
$route['cobros_seguros/registrar-pago-cobro/(:any)'] = 'cobros_seguros/registrar_pago_cobro/$1';
$route['cobros_seguros/ver/(:any)'] = 'cobros_seguros/ver/$1';
$route['cobros_seguros/ajax-listar'] = 'cobros_seguros/ajax_listar';

$route['cobros_seguros/ajax-get-cobro'] = 'cobros_seguros/ajax_get_cobro';
$route['cobros_seguros/ajax-formulario-catalogos'] = 'cobros_seguros/ajax_formulario_catalogos';



///clientes con facturas
$route['cobros_seguros/ajax-clientes-cobros'] = 'cobros_seguros/ajax_clientes_cobros';
///busca las facturas
$route['cobros_seguros/ajax-facturas-cobros'] = 'cobros_seguros/ajax_facturas_cobros';
//buscar cliente especifico con sus facturas
$route['cobros_seguros/ajax-facturas-cliente'] = 'cobros_seguros/ajax_facturas_cliente';
//contratos con facturas
$route['cobros_seguros/ajax-contratos'] = 'cobros_seguros/ajax_contratos';
//contratos con factura info
$route['cobros_seguros/ajax-contrato-facturas'] = 'cobros_seguros/ajax_contrato_facturas';
//Comentarios
$route['cobros_seguros/ajax-guardar-comentario'] = 'cobros_seguros/ajax_guardar_comentario';
$route['cobros_seguros/ajax-cobros-info'] = 'cobros_seguros/ajax_cobros_info';
$route['cobros_seguros/ajax-factura-info'] = 'cobros_seguros/ajax_factura_info';
