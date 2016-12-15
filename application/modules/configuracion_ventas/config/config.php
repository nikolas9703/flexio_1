<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre'        => 'Configuración Ventas',
    'descripcion'   => 'Modulo para Administración de Configuración de Ventas.',
    'autor'         => 'Pensanomica Team',
    'icono'         => 'fa-briefcase',
    'version'       => '1.0',
    'tipo'          => 'addon', // core, addon
    'grupo'         => 'Configuración',
    'agrupador_orden'     => 15,
    'agrupador'		=> array(
        'Ventas' => array(
            "grupo_orden" => 9
        ),
    ),
    'prefijo'       => 'confcli',
    'menu' => array(
        'nombre'    => 'Configuración' ,
        'url'       => 'configuracion_ventas/listar'
    ),
    'permisos'      => array(
        'acceso' => 'Acceso',
    )
);