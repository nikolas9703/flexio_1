<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Configuracion Recursos Humanos',
	'descripcion'	=> 'Modulo para configuracion de Recursos Humanos.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-child',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Configuracion',
	//'agrupador'		=> 'Recursos Humanos',
    'agrupador'		=> array(
        'Recursos Humanos' => array(
            "grupo_orden" => 8
        ),
    ),
	'prefijo'		=> 'col',
	'menu' => array(
		'nombre' =>'Configuracion' ,
		'url' => 'configuracion_rrhh/listar',
		'orden'=> 6
	),
    'permisos'		=> array(
		'acceso' => 'Acceso',
    	'configuracion__crearCargo' => 'Crear Cargo',
    	'configuracion__editarCargo' => 'Editar Cargo',
    	'configuracion__duplicarCargo' => 'Duplicar Cargo',
    	'configuracion__desactivarActivarCargo' => 'Desactivar/Activar Cargo',
    	'configuracion__crearAreaNegocio' => 'Crear &Aacute;rea de Negocio',
	)
);
