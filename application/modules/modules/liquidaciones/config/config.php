<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Liquidaciones',
	'descripcion'	=> 'Modulo para Administracion de liquidaciones de colaboradores.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-child',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Liquidaciones',
	'agrupador'		=> 'Recursos Humanos',
	'prefijo'		=> 'liq',
    'permisos'		=> array(
		'acceso' => 'Acceso',
	)
);
