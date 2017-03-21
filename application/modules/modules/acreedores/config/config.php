<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre'        => 'Acreedores',
    'descripcion'   => 'Modulo para Administracion de Acreedores.',
    'autor'         => 'Pensanomica Team',
    'icono'         => 'fa-users',
    'version'       => '1.0',
    'tipo'          => 'addon', // core, addon
    'grupo'         => 'Acreedores',
    'prefijo'       => 'pro',//el modulo de acreedores usa las tables de proveedores "pro_proveedores..."
	'agrupador'		=> array(
		 'Recursos Humanos' => array(
            "grupo_orden" => 4
        ),
		'Nomina' => array(
            "grupo_orden" => 3
        ),
            'Administración' => array(
            "grupo_orden" => 9
        )
	),
	'agrupador_orden'     => 2,
	'menu' => array(
        'nombre'    => 'Acreedores' ,
        'url'       => 'acreedores/listar',
    	'orden'		=> 0
    ),
    'permisos'      => array(
        'acceso'                        => 'Acceso',
    	'listar__exportarAcreedores'    => 'Exportar',
    	'ver__editarAcreedor'           => 'Editar',
    )
);
 