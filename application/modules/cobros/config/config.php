<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Cobros',
	'descripcion'	=> 'Modulo para Administracion de cobros.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-line-chart',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Cobros',
	//'agrupador'		=> 'Ventas',
    'agrupador'		=> array(
        'Ventas' => array(
            "grupo_orden" => 5
        ),
    ),
	'agrupador_orden'		=> 8,
	'prefijo'		=> 'cob',
	'menu' => array(
		'nombre' =>'Cobros' ,
		'url' => 'cobros/listar',
		'orden'=> 4
	),
        'permisos'		=> array(
		'acceso' => 'Acceso',
    'listar__ver' => 'Listar',
              //  'editar' => 'Editar',
              //  'crear'  => 'Crear'
	)
);
