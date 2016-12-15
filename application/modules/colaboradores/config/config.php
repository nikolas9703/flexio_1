<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Colaboradores',
	'descripcion'	=> 'Modulo para Administracion de colaboradores.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-child',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Colaboradores',
	'agrupador'		=> array(
 	    'Recursos Humanos' => array(
	        "grupo_orden" => 1
	    ),
		 'Nomina' => array(
                "grupo_orden" => 1
            )
	),
	'prefijo'		=> 'col',
	'menu' => array(
		'nombre' =>'Colaboradores' ,
		'url' => 'colaboradores/listar',
		'orden'=> 0
	),
    'permisos'		=> array(
		'acceso' => 'Acceso',
    	'ver__editarColaborador' => 'Editar Colaborador'
	)
);
 