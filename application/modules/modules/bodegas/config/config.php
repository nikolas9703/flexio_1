<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre'        => 'Bodegas',
    'descripcion'   => 'Modulo para Administracion de Bodegas.',
    'autor'         => 'Pensanomica Team',
    'icono'         => 'fa-cubes',
    'version'       => '1.0',
    'tipo'          => 'addon', // core, addon
    'grupo'         => 'Bodegas',
    'agrupador'     => array(
    	'Inventario' => array(
			"grupo_orden" => 3
		),
    	'Servicios' => array(
			"grupo_orden" => 3
		)
    ),
    'prefijo'       => 'bod',
    'menu' => array(
        'nombre'    => 'Bodegas' ,
        'url'       => 'bodegas/listar',
        'orden'     => 35
    ),
    'permisos'      => array(
        'acceso'                    => 'Acceso',
    	'listar__exportarBodegas'   => 'Exportar',
    	'ver__editarBodega'         => 'Editar',
	)
);