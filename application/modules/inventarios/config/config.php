<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre'        => 'Inventarios',
    'descripcion'   => 'Modulo para Administracion de Inventarios.',
    'autor'         => 'Pensanomica Team',
    'icono'         => 'fa-cubes',
    'version'       => '1.0',
    'tipo'          => 'addon', // core, addon
    'grupo'         => 'Inventario',
    'agrupador'     => array(
        'Contratos' => array(
			"grupo_orden" => 5
		),
        'Inventario' => array(
			"grupo_orden" => 0
		),
        'Alquileres' => array(
			"grupo_orden" => 3
		),
		'Servicios' => array(
			"grupo_orden" => 2
		)
    ),
    'prefijo'       => 'inv',
    'menu' => array(
        'nombre'    => 'Items' ,
        'url'       => 'inventarios/listar',
        'orden'     => 0
    ),
    'permisos'      => array(
        'acceso'                        => 'Acceso',
    	'listar__exportarInventarios'   => 'Exportar',
    	'ver__editarInventario'         => 'Editar',
    )
);