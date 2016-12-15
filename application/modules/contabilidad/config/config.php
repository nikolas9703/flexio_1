<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Contabilidad',
	'descripcion'	=> 'Modulo para Administracion de contabilidad.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-calculator',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Admin Contable',
	//'agrupador'		=> 'Contabilidad',
    'agrupador'		=> array(
        'Contabilidad' => array(
            "grupo_orden" => 1
        ),
    ),
	'prefijo'		=> 'contab',
	'menu' => array(
	    array(
	        'nombre' =>'Centros Contables' ,
	        'url' => 'contabilidad/listar_centros_contables',
	        'orden' =>1
	    ),
		array(
		          'nombre' =>'Plan Contable' ,
		          'url' => 'contabilidad/listar',
			      'orden' =>0
		),
	),
        'permisos'		=> array(
            'acceso' => 'Acceso',
            //'listar' => 'Listas Cuentas',
            //'editar' => 'Editar Cuentas',
            //'crear'  => 'Crear Cuentas'
	)
);
