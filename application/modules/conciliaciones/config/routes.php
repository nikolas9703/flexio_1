<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['conciliaciones/listar']         = 'conciliaciones/listar';
$route['conciliaciones/crear']          = 'conciliaciones/crear';
$route['conciliaciones/ver/(:any)']     = 'conciliaciones/ver/$1';

//peticiones ajax
$route['conciliaciones/ajax-listar']            = 'conciliaciones/ajax_listar';
$route['conciliaciones/ajax-get-transacciones'] = 'conciliaciones/ajax_get_transacciones';
$route['conciliaciones/ajax-conciliacion'] = 'conciliaciones/ajax_conciliacion';
