<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['cobros/listar'] = 'cobros/listar';
$route['cobros/crear'] = 'cobros/crear';
$route['cobros/ver/(:any)'] = 'cobros/ver/$1';
$route['cobros/registrar-pago/(:any)'] = 'cobros/registrar_pago/$1';
$route['cobros/registrar-pago-cobro/(:any)'] = 'cobros/registrar_pago_cobro/$1';
$route['cobros/ver/(:any)'] = 'cobros/ver/$1';
$route['cobros/ajax-listar'] = 'cobros/ajax_listar';

$route['cobros/ajax-get-cobro'] = 'cobros/ajax_get_cobro';
$route['cobros/ajax-formulario-catalogos'] = 'cobros/ajax_formulario_catalogos';



///clientes con facturas
$route['cobros/ajax-clientes-cobros'] = 'cobros/ajax_clientes_cobros';
///busca las facturas
$route['cobros/ajax-facturas-cobros'] = 'cobros/ajax_facturas_cobros';
//buscar cliente especifico con sus facturas
$route['cobros/ajax-facturas-cliente'] = 'cobros/ajax_facturas_cliente';
//contratos con facturas
$route['cobros/ajax-contratos'] = 'cobros/ajax_contratos';
//contratos con factura info
$route['cobros/ajax-contrato-facturas'] = 'cobros/ajax_contrato_facturas';
//Comentarios
$route['cobros/ajax-guardar-comentario'] = 'cobros/ajax_guardar_comentario';
$route['cobros/ajax-cobros-info'] = 'cobros/ajax_cobros_info';
$route['cobros/ajax-factura-info'] = 'cobros/ajax_factura_info';
