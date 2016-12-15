<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre'        => 'Entradas',
    'descripcion'   => 'Modulo para Administracion de Entradas de Inventario.',
    'autor'         => 'Pensanomica Team',
    'icono'         => 'fa-cubes',
    'version'       => '1.0',
    'tipo'          => 'addon', // core, addon
    'grupo'         => 'Movimientos',
    'agrupador'     => 'Inventario',
    'prefijo'       => 'ent',
    'menu' => array(
        'nombre'    => 'Entradas' ,
        'url'       => 'entradas/listar',
        'orden'     => 20
    ),
    'permisos'      => array(
        'acceso'                    => 'Acceso',
    	'listar__exportarEntradas'  => 'Exportar',
    	'ver__editarEntrada'        => 'Editar',
    )
);