<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$route['clientes_abonos/crear']  = 'clientes_abonos/crear';
$route['clientes_abonos/listar']  = 'clientes_abonos/listar';
$route['clientes_abonos/ajax-abonos-info']          = 'clientes_abonos/ajax_abonos_info';
$route['clientes_abonos/ajax-factura-info']         = 'clientes_abonos/ajax_factura_info';
$route['clientes_abonos/ajax-info-abono']           = 'clientes_abonos/ajax_info_abono';
$route['clientes_abonos/ver/(:any)']                = 'clientes_abonos/ver/$1';
$route['clientes_abonos/editarsubpanel']            = 'clientes_abonos/editarsubpanel';
$route['clientes_abonos/crearsubpanel']            = 'clientes_abonos/crearsubpanel';
$route['clientes_abonos/editar-subpanel']          = 'clientes_abonos/editar_clientes_abonos_subpanel';

$route['clientes_abonos/ajax-clientes']             = 'clientes_abonos/ajax_clientes';

$route['clientes_abonos/ajax-facturas-abonos']      = 'clientes_abonos/ajax_facturas_abonos';

$route['clientes_abonos/ajax-cliente-info']         = 'clientes_abonos/ajax_cliente_info';
$route['clientes_abonos/ajax-listar']               = 'clientes_abonos/ajax_listar';