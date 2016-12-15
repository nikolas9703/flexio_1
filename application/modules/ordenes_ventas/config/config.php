<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Ordenes de Venta',
	'descripcion'	=> 'Modulo para Administracion de Ordenes de Venta.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-line-chart', 
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Ordenes de Venta',
	//'agrupador'		=> 'Ventas',
    'agrupador'		=> array(
         'Contratos' => array(
             "grupo_orden" =>7
	),
        'Ventas' => array(
            "grupo_orden" => 3
        )
     ),
	'prefijo'		=> 'ord',
	'menu' => array(
		'nombre' =>'Ordenes de Venta' ,
		'url' => 'ordenes_ventas/listar',
		'orden'=> 2
	),
    'permisos'		=> array(
		'acceso' => 'Acceso',
		'Ordenes_ventasver__descuentoOrdenesVentas' => 'Aplicar Descuento',
		'ver__editarPrecioOrdenes' => 'Editar Precio',
		'crear__editarPrecioOrdenes' => 'Editar Precio'
	)
);
