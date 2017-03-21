<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Depreciacion de Activo Fijo',
	'descripcion'	=> 'Modulo para Administracion de depreciacion de activo fijo.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-line-chart',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Trans Contables',
	'agrupador'		=> 'Contabilidad',
    'prefijo'		=> 'dep',
	'menu' => array(
		'nombre' =>'Depreciacion Act Fijos',
		'url' => 'depreciacion_activos_fijos/listar',
		'orden'=> 2
	),
	'prefijo'		=> 'cont',
    'permisos'		=> array(
		'acceso' => 'Acceso',
    	//'listar' => 'Clientes',
    	//'crear' => 'Crear'
	)
);
