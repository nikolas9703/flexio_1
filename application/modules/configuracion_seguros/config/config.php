<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre'        => 'Configuración seguros',
    'descripcion'	=> 'Modulo para Configuración de las aseguradoras.',
    'autor'			=> 'Pensanomica Team',
    'icono'			=> 'fa-book',
    'version'		=> '1.0',
    'tipo'			=> 'addon', //core, addons
    'grupo'			=> 'Configuración',
    'agrupador'		=> array(
        'Seguros' => array(
            "grupo_orden" => 4
        ),
    ),
    'prefijo'   => 'seg',
    'permisos'	=> array(
        'acceso'                                    => 'Acceso',
        'listar-usuarios__desactivarActivarUsuario' => 'Desactivar/Activar cuenta de Usuarios',
    )
);