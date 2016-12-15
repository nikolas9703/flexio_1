<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$route['presupuesto/listar']        = 'presupuesto/listar';
$route['presupuesto/ocultotabla']   = 'presupuesto/ocultotabla';

//Ajax presupuesto
$route['presupuesto/ajax-listar']               = 'presupuesto/ajax_listar';
$route['presupuesto/ajax-armarPresupuesto'] = 'presupuesto/ajax_armarPresupuesto';
$route['presupuesto/ajax-armarPresupuestoVer'] = 'presupuesto/ajax_armarPresupuestoVer';


//Formulario crear/editar
$route['presupuesto/crear']         = 'presupuesto/crear';
$route['presupuesto/historial']         = 'presupuesto/historial';
$route['presupuesto/ver/(:any)']    = 'presupuesto/ver/$1';
$route['presupuesto/detalle/(:any)']    = 'presupuesto/detalle/$1';

//funcionalidad

$route['presupuesto/guardar']               = 'presupuesto/guardar';
$route['presupuesto/exportar']               = 'presupuesto/exportar';
