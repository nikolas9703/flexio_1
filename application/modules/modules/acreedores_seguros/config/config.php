<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre'        => 'Acreedores seguro',
    'descripcion'   => 'Modulo para Administracion de Acreedores de seguro.',
    'autor'         => 'Pensanomica Team',
    'icono'         => 'fa-users',
    'version'       => '1.0',
    'tipo'          => 'addon', // core, addon
    'grupo'         => 'Administración',
    'prefijo'       => 'acre',//el modulo de acreedores usa las tables de proveedores "pro_proveedores..."
    'agrupador'		=> array(
            'Seguros' => array(
                "grupo_orden" => 3
            ),
        ),
    
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
 