<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre'        => 'Salidas',
    'descripcion'   => 'Modulo para Administracion de Salidas de Inventario.',
    'autor'         => 'Pensanomica Team',
    'icono'         => 'fa-cubes',
    'version'       => '1.0',
    'tipo'          => 'addon', // core, addon
    'grupo'         => 'Movimientos',
    'agrupador'     => 'Inventario',
    'prefijo'       => 'sal',
    'menu' => array(
        'nombre'    => 'Salidas' ,
        'url'       => 'salidas/listar',
        'orden'     => 25
    ),
    'permisos'      => array(
        'acceso'                    => 'Acceso',
    	'listar__exportarSalidas'   => 'Exportar',
    	'ver__editarSalida'         => 'Editar',
    )
);