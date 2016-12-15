<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre'        => 'Traslados',
    'descripcion'   => 'Modulo para Administracion de Traslados de Inventario.',
    'autor'         => 'Pensanomica Team',
    'icono'         => 'fa-cubes',
    'version'       => '1.0',
    'tipo'          => 'addon', // core, addon
    'grupo'         => 'Movimientos',
    'agrupador'     => 'Inventario',
    'prefijo'       => 'tras',
    'menu' => array(
            'nombre'    => 'Traslados' ,
            'url'       => 'traslados/listar',
            'orden'     => 15
     ),
    'permisos'      => array(
        'acceso'                    => 'Acceso',
    	'listar__exportarTraslados' => 'Exportar',
    	'ver__editarTraslado'       => 'Editar',
	)
);