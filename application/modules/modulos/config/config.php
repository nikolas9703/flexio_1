<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Administrador de Modulos',
	'descripcion'	=> 'Permite desinstalar o instalar modulos a la herramienta.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-archive',
	'version'		=> '1.1',
	'tipo'			=> 'core', // core, addon
	'grupo'			=> 'Administracion de Sistema',
        'permisos'	=> array(
            'acceso' => 'Acceso'
	)
);