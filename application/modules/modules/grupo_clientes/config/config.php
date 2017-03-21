<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Grupos de Clientes',
	'descripcion'	=> 'Modulo para Administracion de grupos de clientes.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-line-chart',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Ventas',
	'agrupador'		=> [
                'Contratos',
		'Ventas',
		'Alquileres',
		'Servicios',
                'Seguros'
    ],
	'menu' => array(
		'nombre' =>'Grupos de Clientes' ,
		'url' => 'grupo_clientes/listar',
		'orden'=> 1
	),
	'prefijo'		=> 'grp',
    'permisos'		=> array(
		'acceso' => 'Acceso',
	)
);
