<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

$route['notas_debitos/listar'] = 'notas_debitos/listar';
$route['notas_debitos/crear'] = 'notas_debitos/crear';
$route['notas_debitos/ver/(:any)'] = 'notas_debitos/ver/$1';
//ajax
$route['notas_debitos/ajax-listar'] = 'notas_debitos/ajax_listar';
$route['notas_debitos/ajax-guardar-comentario'] = 'notas_debitos/ajax_guardar_comentario';
