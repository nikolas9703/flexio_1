<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['catalogos/ver'] = 'catalogos/ver';
//$route['catalogos/ver-catálogos'] = 'catalogos/crear';

$route['crear Planes'] = 'planes/crear';
$route['listar Planes'] = 'planes/listar';
$route['editar Planes'] = 'planes/editar';
$route['ver Planes'] = 'planes/ver';

$route['crear Ramos'] = 'ramos/crear';
$route['listar Ramos'] = 'ramos/listar';
$route['editar Ramos'] = 'ramos/editar';
$route['ver Ramos'] = 'ramos/ver';


//$route['ver Configuracion'] = 'catalogos/configuracion';
$route['catalogos/ajax-listar-ramos'] = 'Configuracion_seguros/ajax_listar_ramos';
$route['catalogos/ajax-listar-ramos-tree'] = 'Configuracion_seguros/ajax_listar_ramos_tree';
$route['catalogos/ajax-cambiar-estado-ramo'] = 'Configuracion_seguros/ajax_cambiar_estado_ramo';
$route['catalogos/ajax-guardar-ramos'] = 'Configuracion_seguros/ajax_guardar_ramos';
$route['catalogos/ajax-buscar-ramo'] = 'Configuracion_seguros/ajax_buscar_ramo';
//$route['catalogos/editar'] = 'catalogos/editar';

?>
