<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['abonos/listar']                          = 'abonos/listar';
$route['abonos/crear']                           = 'abonos/crear';
$route['abonos/guardar']                         = 'abonos/guardar';
$route['abonos/ver/(:any)']                      = 'abonos/ver/$1';
$route['abonos/registrar-abono/(:any)']           = 'abonos/registrar_abono/$1';
$route['abonos/registrar-abono-abono/(:any)']     = 'abonos/registrar_abono_abono/$1';
$route['abonos/ver/(:any)']                      = 'abonos/ver/$1';
$route['abonos/ajax-listar']                     = 'abonos/ajax_listar';
$route['abonos/ajax-abonos-info']                 = 'abonos/ajax_abonos_info';
$route['abonos/ajax-factura-info']               = 'abonos/ajax_factura_info';
$route['abonos/ajax-info-abono']                 = 'abonos/ajax_info_abono';

///proveedores
$route['abonos/ajax-proveedores']                   = 'abonos/ajax_proveedores';
///busca las facturas
$route['abonos/ajax-facturas-abonos']             = 'abonos/ajax_facturas_abonos';
//buscar proveedor especifico con sus facturas
$route['abonos/ajax-proveedor-info']                = 'abonos/ajax_proveedor_info';
