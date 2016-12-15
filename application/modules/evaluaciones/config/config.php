<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Evaluaciones',
	'descripcion'	=> 'Modulo para Administracion de evaluaciones a colaboradores.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-file-text-o',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Evaluaciones',
	'agrupador'		=> 'Recursos Humanos',
	'prefijo'		=> 'evc',
	'menu' => array(),
    'permisos'		=> array(
		'acceso' => 'Acceso',
	)
);
