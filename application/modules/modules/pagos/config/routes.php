<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['pagos/listar'] = 'pagos/listar';
$route['pagos/crear'] = 'pagos/crear';
//$route['pagos/listar/(:any)'] = 'pagos/listar/$1';
//no se para que se usa crear2
$route['pagos/crear2']                          = 'pagos/crear2';
$route['pagos/guardar']                         = 'pagos/guardar';
$route['pagos/ver/(:any)']                      = 'pagos/ver/$1';
$route['pagos/registrar-pago/(:any)']           = 'pagos/registrar_pago/$1';
$route['pagos/registrar-pago-pago/(:any)']     = 'pagos/registrar_pago_pago/$1';
$route['pagos/ver/(:any)']                      = 'pagos/ver/$1';
$route['pagos/ajax-listar']                     = 'pagos/ajax_listar';
$route['pagos/ajax-exportar']                   = 'pagos/ajax_exportar';
$route['pagos/ajax-pagos-info']                 = 'pagos/ajax_pagos_info';
$route['pagos/ajax-formas-pago']                 = 'pagos/ajax_catalogo_pagos';
$route['pagos/ajax-factura-info']               = 'pagos/ajax_factura_info';
$route['pagos/ajax-info-pago']                 = 'pagos/ajax_info_pago';
$route['pagos/ajax-guardar-comentario']        = 'pagos/ajax_guardar_comentario';
$route['pagos/ajax-guardar-documentos'] = 'pagos/ajax_guardar_documentos';
$route['pagos/ajax-anularpago-colaborador'] = 'pagos/ajax_anularpago_colaborador';


$route['pagos/ajax-cambiar-estado'] = 'pagos/ajax_cambiar_estado';
///proveedores con facturas
$route['pagos/ajax-proveedores-pagos']          = 'pagos/ajax_proveedores_pagos';
///subcontratos con facturas por pagar o pagadas parciales
$route['pagos/ajax-subcontratos-pagos']         = 'pagos/ajax_subcontratos_pagos';
///busca las facturas
$route['pagos/ajax-facturas-pagos']             = 'pagos/ajax_facturas_pagos';
//buscar proveedor especifico con sus facturas
$route['pagos/ajax-facturas-proveedor']           = 'pagos/ajax_facturas_proveedor';
//buscar subcontrato especifico con sus facturas
$route['pagos/ajax-facturas-subcontrato']       = 'pagos/ajax_facturas_subcontrato';

//Confirmacion de pago para los colaboradores
$route['pagos/ajax-pagar-colaborador']       = 'pagos/ajax_pagar_colaborador';


//Confirmacion de pago para los colaboradores en los pagos extraordinarios
$route['pagos/ajax-pagar-colaborador-pagoextraordinario']       = 'pagos/ajax_pagar_colaborador_pagoextraordinario';

//Confirmacion de pago para los colaboradores
$route['pagos/ajax-generar-ach']       = 'pagos/ajax_generar_ach';

$route['pagos/ajax-aprobar-pago']       = 'pagos/ajax_aprobar_pago';

//Confirmaciocion para aplicar pago
$route['pagos/ajax-aplicar-pago']       = 'pagos/ajax_aplicar_pago';

//Confirmaciocion para anular pago
$route['pagos/ajax-anular-pago'] = 'pagos/ajax_anular_pago';

//others
$route['pagos/ajax-get-empezables'] = 'pagos/ajax_get_empezables';
$route['pagos/ajax-get-empezable'] = 'pagos/ajax_get_empezable';



//Obtinene los agentes y los provedores 
$route['pagos/ajax_agentes_proovedores'] = 'pagos/ajax_agentes_proovedores';
