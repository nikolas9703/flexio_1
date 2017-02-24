<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Facturas de Seguros',
	'descripcion'	=> 'Modulo para Administracion de facturas de seguros.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-line-chart',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Facturas',
  	'prefijo'		=> 'fse',
  	'agrupador'		=> array(
        'Seguros' => array(
            "grupo_orden" => 3
        ),
    ),
	'menu' => array(
		'nombre' =>'Facturas' ,
		'url' => 'facturas_seguros/listar',
		'orden'=> 5
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
