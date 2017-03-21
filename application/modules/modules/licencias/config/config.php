<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Licencias',
	'descripcion'	=> 'Modulo para Administracion de licencias de colaboradores.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-child',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Licencias',
	'agrupador'		=> 'Recursos Humanos',
	'prefijo'		=> 'lic',
    'permisos'		=> array(
		'acceso' => 'Acceso',
	)
);
