<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Vacaciones',
	'descripcion'	=> 'Modulo para Administracion de vacaciones de colaboradores.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-child',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Vacaciones',
	'agrupador'		=> 'Recursos Humanos',
	'prefijo'		=> 'vac',
    'permisos'		=> array(
		'acceso' => 'Acceso',
	)
);
