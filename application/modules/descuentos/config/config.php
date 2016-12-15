<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Descuentos',
	'descripcion'	=> 'Modulo para Administracion de descuentos a colaboradores.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-child',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Descuentos Directos',
	'prefijo'		=> 'desc',
    'agrupador'		=> array(
    		'Recursos Humanos' => array(
                "grupo_orden" => 3
             ),
    		'Nomina' => array(
                "grupo_orden" => 4
            )
	),
    
	'menu' => array(
		'nombre' =>'Descuentos Directos' ,
		'url' => 'descuentos/listar',
		'orden'=> 2
	),
    'permisos'		=> array(
		'acceso' => 'Acceso',
    	'ver__editarDescuento' => 'Editar Descuento',
	)
);
 