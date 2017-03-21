<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Ordenes de Trabajo',
	'descripcion'	=> 'Modulo para Administracion de Ordenes de Trabajo.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-wrench',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Servicios',
	'agrupador'		=> array(
		'Servicios' => array(
			"grupo_orden" => 2
		),
	),
	'prefijo'		=> 'odt',
	'menu' => array(
		'nombre' =>'Ordenes de Trabajo' ,
		'url' => 'ordenes_trabajo/listar',
		'orden'=> 1
	),
    'permisos'		=> array(
		'acceso' => 'Acceso'
	)
);
