<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Ausencias',
	'descripcion'	=> 'Modulo para Administracion de ausencias de colaboradores.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-child',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Ausencias',
	'agrupador'		=> 'Recursos Humanos',
	'prefijo'		=> 'aus',
    'permisos'		=> array(
		'acceso' => 'Acceso',
	)
);
