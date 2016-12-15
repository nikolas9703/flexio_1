<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre'        => 'Consumos',
    'descripcion'   => 'Modulo para Administracion de Consumos de Inventario.',
    'autor'         => 'Pensanomica Team',
    'icono'         => 'fa-cubes',
    'version'       => '1.0',
    'tipo'          => 'addon', // core, addon
    'grupo'         => 'Movimientos',
    'agrupador'     => 'Inventario',
    'prefijo'       => 'cons',
    'menu' => array(
        'nombre'    => 'Consumo' ,
        'url'       => 'consumos/listar',
        'orden'     => 30
    ),
    'permisos'      => array(
        'acceso'                    => 'Acceso',
    	'listar__exportarConsumos'  => 'Exportar',
    	'ver__editarConsumo'        => 'Editar',
	)
);