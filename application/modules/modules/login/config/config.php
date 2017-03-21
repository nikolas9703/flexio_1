<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Login',
	'descripcion'	=> 'Modulo para administrar el acceso al sistema.',
	'autor'			=> 'Pensanomica Team',
	'version'		=> '1.2',
	'tipo'			=> 'core', // core, addon
	'grupo'			=> 'configuracion',
        'permisos'	=> array(
            'acceso' => 'Acceso'
	)
);