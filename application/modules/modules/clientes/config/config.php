<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Clientes',
	'descripcion'	=> 'Modulo para Administracion de clientes.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-line-chart',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Ventas',
	'agrupador'		=> array(
               'Contratos' => array(
                    "grupo_orden" => 0
		),
                'Ventas' => array(
                    "grupo_orden" => 0
		),
                'Alquileres' => array(
                    "grupo_orden" => 0
		),
		'Servicios' => array(
                    "grupo_orden" => 0
		),
                'Seguros' => array(
                    "grupo_orden" => 0
		),
        ),
	'agrupador_orden'     => '1',
	'menu' => array(
		'nombre' =>'Clientes' ,
		'url' => 'clientes/listar',
		'orden'=> 0
	),
	'prefijo'		=> 'cli',
    'permisos'		=> array(
		'acceso' => 'Acceso',
		'crear__validarDuplicado'          => 'Validar Duplicado',
		'listar__convertirInteres' => 'Convertir a bien'
    	//'listar' => 'Clientes',
    	//'crear' => 'Crear'
	)
);
