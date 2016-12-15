<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre'        => 'Cajas',
    'descripcion'   => 'Modulo para Administracion de Caja Menuda.',
    'autor'         => 'Pensanomica Team',
    'icono'         => 'fa-shopping-cart',
    'version'       => '1.0',
    'tipo'          => 'addon', // core, addon
    'grupo'         => 'Cajas',
    //'agrupador'     => 'Compras',
    'agrupador'		=> array
        (
            'Compras' => array
            (
                "grupo_orden" => 8
            ),
            'Ventas' => array
            (
                "grupo_orden" => 8
            ),
        ),
    'prefijo'       => 'CA',
    'menu' => array(
        'nombre'    => 'Cajas' ,
        'url'       => 'cajas/listar',
    	'orden'		=> 6
    ),
    'permisos'      => array(
        'acceso'                    => 'Acceso',
        'listar__exportarCaja'  => 'Exportar',
    	'listar__ver'   => 'Listar',
    )
);
