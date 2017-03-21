<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre'        => 'Pedidos',
    'descripcion'   => 'Modulo para Administracion de Pedidos.',
    'autor'         => 'Pensanomica Team',
    'icono'         => 'fa-building',
    'version'       => '1.0',
    'tipo'          => 'addon', // core, addon
    'grupo'         => 'Pedidos',
    //'agrupador'     => 'Compras',
    'agrupador'		=> array(
        'Compras' => array(
            "grupo_orden" => 2
        ),
    ),
    'prefijo'       => 'ped',
    'menu' => array(
        'nombre'    => 'Pedidos' ,
        'url'       => 'pedidos/listar',
    	'orden'		=> 1
    ),
    'permisos'      => array(
        'acceso'                    => 'Acceso',
    	'listar__exportarPedidos'   => 'Exportar',
    	'ver__editarPedido'         => 'Editar',
    )
);


				
	 