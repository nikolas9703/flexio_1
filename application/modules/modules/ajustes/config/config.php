<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre'        => 'Ajustes',
    'descripcion'   => 'Modulo para Administracion de Ajustes de Inventario.',
    'autor'         => 'Pensanomica Team',
    'icono'         => 'fa-cubes',
    'version'       => '1.0',
    'tipo'          => 'addon', // core, addon
    'grupo'         => 'Inventario',
    'agrupador'     => 'Inventario',
	'agrupador_orden'     => 6,
    'prefijo'       => 'aju',
    'menu' => array(
        'nombre'    => 'Ajustes' ,
        'url'       => 'ajustes/listar',
        'orden'     => 10
    ),
    'permisos'      => array(
        'acceso'                    => 'Acceso',
    	'listar__exportarAjustes'   => 'Exportar',
    	'ver__editarAjuste'         => 'Editar',
	)
);