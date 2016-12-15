<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['series/listar'] = 'series/listar';
$route['series/ver/(:any)'] = 'series/ver/$1';

//ajax
$route['series/ajax-listar'] = 'series/ajax_listar';
