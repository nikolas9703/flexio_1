<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Cobros de Seguros',
	'descripcion'	=> 'Modulo para Administracion de cobros de seguros.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-line-chart',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Cobros',
  	'prefijo'		=> 'fse',
  	'agrupador'		=> array(
        'Seguros' => array(
            "grupo_orden" => 4
        ),
    ),
	'menu' => array(
		'nombre' =>'Cobros' ,
		'url' => 'cobros_seguros/listar',
		'orden'=> 6
	),
    'permisos'		=> array(
		'acceso' => 'Acceso',
		'cambiarEstado' => 'Cambiar Estado',
    	//'listar__ver' => 'Listar',
    	'crear__editarPrecio' => 'Editar Precio',
    	'ver__editarPrecio' => 'Editar Precio',
        //  'crear'  => 'Crear'

	)
);
