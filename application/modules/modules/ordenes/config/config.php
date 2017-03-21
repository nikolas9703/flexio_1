<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre'        => 'Ordenes',
    'descripcion'   => 'Modulo para Administracion de Ordenes.',
    'autor'         => 'Pensanomica Team',
    'icono'         => 'fa-building',
    'version'       => '1.0',
    'tipo'          => 'addon', // core, addon
    'grupo'         => 'Órdenes de Compra',
   // 'agrupador'     => 'Compras',
    'agrupador'		=> array(
        'Compras' => array(
            "grupo_orden" => 3
        ),
    ),
    'prefijo'       => 'ord',
    'menu' => array(
        'nombre'    => 'Órdenes de Compra' ,
        'url'       => 'ordenes/listar',
    	'orden'		=> 2
    ),
    'permisos'      => array(
        'acceso'                    => 'Acceso',
    	'listar__exportarOrdenes'   => 'Exportar',
    	'ver__editarOrdenes'         => 'Editar',
    )
);