<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Permisos',
	'descripcion'	=> 'Modulo para Administracion de permisos de colaboradores.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-child',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Permisos',
	'agrupador'		=> 'Recursos Humanos',
	'prefijo'		=> 'perm',
    'permisos'		=> array(
		'acceso' => 'Acceso',
	)
);
