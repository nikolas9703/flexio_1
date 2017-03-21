<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['reportes_financieros'] = 'reportes_financieros';
$route['reportes_financieros/listar'] = 'reportes_financieros';
$route['reportes_financieros/reporte/(:any)'] = 'reportes_financieros/reporte/$1';


//ajax
$route['reportes_financieros/ajax-formulario-datos'] = 'reportes_financieros/ajax_formulario_datos';
$route['reportes_financieros/ajax-generar-reporte'] = 'reportes_financieros/ajax_generar_reporte';
