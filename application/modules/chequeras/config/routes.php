<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['chequeras/listar'] = 'chequeras/listar';
$route['chequeras/crear'] = 'chequeras/crear';
$route['chequeras/guardar'] = 'chequeras/guardar';
$route['chequeras/ver/(:any)'] = 'chequeras/ver/$1';
$route['chequeras/ajax-listar'] = 'chequeras/ajax_listar';
$route['chequeras/ajax-cheque-info'] = 'chequeras/ajax_cheque_info';
$route['chequeras/ajax-getAll'] = 'chequeras/ajax_getAll';