<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['subcontratos/listar'] = 'subcontratos/listar';
$route['subcontratos/crear'] = 'subcontratos/crear';
$route['subcontratos/ver/(:any)'] = 'subcontratos/ver/$1';
$route['subcontratos/agregar_adenda/(:any)'] = 'subcontratos/agregar_adenda/$1';
$route['subcontratos/editar_adenda/(:any)'] = 'subcontratos/editar_adenda/$1';
//peticiones ajax
$route['subcontratos/ajax-listar']              = 'subcontratos/ajax_listar';
$route['subcontratos/ajax-listar-adendas']      = 'subcontratos/ajax_listar_adendas';
$route['subcontratos/ajax-contrato-info']       = 'subcontratos/ajax_contrato_info';
$route['subcontratos/ajax-guardar-comentario']  = 'subcontratos/ajax_guardar_comentario';
$route['subcontratos/ajax-guardar-comentario-subcontrato']  = 'subcontratos/ajax_guardar_comentario_subcontrato';
$route['subcontratos/ajax-guardar-documentos']  = 'subcontratos/ajax_guardar_documentos';
