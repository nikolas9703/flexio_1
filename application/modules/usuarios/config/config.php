<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'          => 'Usuarios',
	'descripcion'	=> 'Modulo para administrar los usuarios del sistema.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-group',
	'version'		=> '1.0',
	'tipo'			=> 'core', //core, addons
	'grupo'			=> 'Administracion de Sistema',
	'prefijo'		=> '',
        'permisos'	=> array(
            'acceso'                                    => 'Acceso',
            'listar-usuarios__desactivarActivarUsuario' => 'Desactivar/Activar cuenta de Usuarios',
	)
);

