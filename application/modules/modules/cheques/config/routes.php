<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['cheques/listar'] = 'cheques/listar';
$route['cheques/crear'] = 'cheques/crear';
$route['cheques/guardar'] = 'cheques/guardar';
$route['cheques/ver/(:any)'] = 'cheques/ver/$1';
$route['cheques/ajax-listar'] = 'cheques/ajax_listar';
$route['cheques/ajax-cheque-info'] = 'cheques/ajax_cheque_info';
$route['cheques/ajax-getAll'] = 'cheques/ajax_getAll';
$route['cheques/ajax-pagos-cheques'] = 'cheques/ajax_pagos_cheques';
$route['cheques/ajax-pago-info'] = 'cheques/ajax_pago_info';
$route['cheques/ajax-info-chequera'] = 'cheques/ajax_info_chequera';
$route['cheques/ajax-pago-proveedor'] = 'cheques/ajax_pago_proveedor';
$route['cheques/ajax-cheque-pago'] = 'cheques/ajax_cheque_pago';
$route['cheques/ajax-info-cheque'] = 'cheques/ajax_info_cheque';
$route['cheques/ajax-anular-cheque'] = 'cheques/ajax_anular_cheque';
$route['cheques/ajax-guardar-comentario'] = 'cheques/ajax_guardar_comentario';
$route['cheques/ajax-cambiando-estado'] = 'cheques/ajax_cambiando_estado';
