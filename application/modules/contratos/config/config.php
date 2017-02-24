<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Contratos de Venta',
	'descripcion'	=> 'Modulo para Administracion de contrato de ventas.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-line-chart',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Precio fijo con clientes',
        'agrupador'		=> array(
               'Contratos' => array(
                    "grupo_orden" => 1
		),
                'Ventas' => array(
                    "grupo_orden" => 4
		)
         ),
    
	'menu' => array(
		'nombre' =>'Contratos de ventas' ,
		'url' => 'contratos/listar',
		'orden'=> 0
	),
	'prefijo'		=> 'cont',
    'permisos'		=> array(
		'acceso' => 'Acceso',
    	//'listar' => 'Clientes',
    	//'crear' => 'Crear'
	)
);
