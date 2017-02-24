<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Notas de Credito',
	'descripcion'	=> 'Modulo para Administracion de Notas de Credito.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-line-chart',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Notas de Crédito',
	//'agrupador'		=> 'Ventas',
    'agrupador'		=> array(
        'Ventas' => array(
            "grupo_orden" => 7
        ),
        'Alquileres' => array(
            "grupo_orden" => 6
        ),
        'Seguros' => array(
            "grupo_orden" => 6
        ),
    ),
	'prefijo'		=> 'ventas',
	'menu' => array(
		'nombre' =>'Notas de Crédito' ,
		'url' => 'notas_creditos/listar',
		'orden'=> 9
	),
    'permisos'		=> array(
		'acceso' => 'Acceso'
	)
);
