<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre'        => 'Politicas de transacciones',
    'descripcion'   => 'Modulo para Administracion de politicas de transacciones.',
    'autor'         => 'Pensanomica Team',
    'icono'         => 'fa-building',
    'version'       => '1.0',
    'tipo'          => 'addon', // core, addon
    'grupo'         => 'Empresa',
     'agrupador'		=> array(
        'Politicas' => array(
            "grupo_orden" => 1
        ),
    ),
    'prefijo'       => 'ptr',
   /* 'menu' => array(
        'nombre'    => 'Pedidos' ,
        'url'       => 'pedidos/listar',
    	'orden'		=> 1
    ),
    'permisos'      => array(
        'acceso'                    => 'Acceso',
    	'listar__exportarPedidos'   => 'Exportar',
    	'ver__editarPedido'         => 'Editar',
    )*/
);


				
	 