<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['anticipos/listar']                          = 'anticipos/listar';
$route['anticipos/crear']                           = 'anticipos/crear';
$route['anticipos/ver/(:any)']                      = 'anticipos/ver/$1';

$route['anticipos/ajax-listar']                     = 'anticipos/ajax_listar';
$route['anticipos/ajax_get_anticipo']                 = 'anticipos/ajax_get_anticipo';
$route['anticipos/ajax_catalogo_formulario_anticipo'] = 'anticipos/ajax_catalogo_formulario_anticipo';
$route['anticipos/ajax-cambiar-estado']                 = 'anticipos/ajax_cambiar_estado';
$route['anticipos/ajax-cambiar-estados']                 = 'anticipos/ajax_cambiar_estados';
$route['anticipos/ajax-guardar-documentos']                 = 'anticipos/ajax_guardar_documentos';
