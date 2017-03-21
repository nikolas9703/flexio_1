<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['devoluciones/listar'] = 'devoluciones/listar';
$route['devoluciones/ajax-listar'] = 'devoluciones/ajax_listar';
$route['devoluciones/crear'] = 'devoluciones/crear';
$route['devoluciones/ver/(:any)'] = 'devoluciones/ver/$1';
$route['devoluciones/guardar'] = 'devoluciones/guardar';
