<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Roles',
	'descripcion'	=> 'Administar roles.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-key',	
	'version'		=> '1.3',
	'tipo'			=> 'core', // core, addon
	'grupo'			=> 'Administracion de Sistema',
        'permisos'	=> array(
            'acceso' => 'Acceso',
            'listar__crear_rol'                 => 'Crear Rol',
            'listar__editar_rol'                => 'Editar Rol',
            'listar__duplicar_rol'              => 'Duplicar Rol',
            'listar__activar_desactivar_rol'    => 'Activar/Desactivar Rol',
	)
);