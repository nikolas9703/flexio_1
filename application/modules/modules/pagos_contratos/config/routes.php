<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['pagos_contratos/listar']                          = 'pagos_contratos/listar';
$route['pagos_contratos/crear']                           = 'pagos_contratos/crear';
$route['pagos_contratos/guardar']                         = 'pagos_contratos/guardar';
$route['pagos_contratos/ver/(:any)']                      = 'pagos_contratos/ver/$1';
$route['pagos_contratos/registrar-pago/(:any)']           = 'pagos_contratos/registrar_pago/$1';
$route['pagos_contratos/registrar-pago-pago/(:any)']      = 'pagos_contratos/registrar_pago_pago/$1';
$route['pagos_contratos/ver/(:any)']                      = 'pagos_contratos/ver/$1';
$route['pagos_contratos/ajax-listar']                     = 'pagos_contratos/ajax_listar';
$route['pagos_contratos/ajax-exportar']                   = 'pagos_contratos/ajax_exportar';
$route['pagos_contratos/ajax-pagos-info']                 = 'pagos_contratos/ajax_pagos_info';
$route['pagos_contratos/ajax-factura-info']               = 'pagos_contratos/ajax_factura_info';
$route['pagos_contratos/ajax-info-pago']                  = 'pagos_contratos/ajax_info_pago';

///proveedores con facturas
$route['pagos_contratos/ajax-proveedores-pagos']          = 'pagos_contratos/ajax_proveedores_pagos';
///subcontratos con facturas por pagar o pagadas parciales
$route['pagos_contratos/ajax-subcontratos-pagos']         = 'pagos_contratos/ajax_subcontratos_pagos';
///busca las facturas
$route['pagos_contratos/ajax-facturas-pagos']             = 'pagos_contratos/ajax_facturas_pagos';
//buscar proveedor especifico con sus facturas
$route['pagos_contratos/ajax-facturas-proveedor']         = 'pagos_contratos/ajax_facturas_proveedor';
//buscar subcontrato especifico con sus facturas
$route['pagos_contratos/ajax-facturas-subcontrato']       = 'pagos_contratos/ajax_facturas_subcontrato';
$route['pagos_contratos/ajax-guardar-comentario']       = 'pagos_contratos/ajax_guardar_comentario';
