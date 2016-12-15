<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['contratos/listar'] = 'contratos/listar';
$route['contratos/crear'] = 'contratos/crear';
$route['contratos/ver/(:any)'] = 'contratos/ver/$1';
$route['contratos/agregar_adenda/(:any)']   = 'contratos/agregar_adenda/$1';
$route['contratos/editar_adenda/(:any)']    = 'contratos/editar_adenda/$1';
//peticiones ajax
$route['contratos/ajax-listar']             = 'contratos/ajax_listar';
$route['contratos/ajax-listar-adendas']     = 'contratos/ajax_listar_adendas';
$route['contratos/ajax-contrato-info']      = 'contratos/ajax_contrato_info';
$route['contratos/ajax-guardar-comentario'] = 'contratos/ajax_guardar_comentario';
