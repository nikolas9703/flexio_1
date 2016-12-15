<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Ordenes de Ventas de Alquiler',
	'descripcion'	=> 'Modulo para Administracion de Ordenes de Venta en Alquileres.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-line-chart',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Ordenes de Venta',
  'agrupador'		=> array(
      'Alquileres' => array(
          "grupo_orden" => 3
      )
   ),
	'prefijo'		=> 'ord',
	'menu' => array(
		'nombre' =>'Ordenes de Venta' ,
		'url' => 'ordenes_alquiler/listar',
		'orden'=> 2
	),
	'permisos'		=> array(
		'acceso' => 'Acceso',
		'ver__editarPrecioOrdenAlquiler' => 'Editar Precio',
		'crear__editarPrecioOrdenAlquiler' => 'Editar Precio'
	)
);
