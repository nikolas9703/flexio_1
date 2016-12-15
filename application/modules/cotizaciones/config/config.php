<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Cotizaciones',
	'descripcion'	=> 'Modulo para Administracion de cotizaciones.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-line-chart',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Cotizaciones',
	'agrupador'		=> array(
                'Contratos' => array(
                    "grupo_orden" => 5
		),
		'Ventas' => array(
			"grupo_orden" => 2
		),
		'Servicios' => array(
			"grupo_orden" => 1
		)
	),
	'prefijo'		=> 'cotz',
	'menu' => array(
		'nombre' =>'Cotizaciones' ,
		'url' => 'cotizaciones/listar',
		'orden'=> 1
	),
        'permisos'		=> array(
            'acceso'        => 'Acceso',
						'ver__editarPrecioCotizacion' => 'Editar Precio',
						'crear__editarPrecioCotizacion' => 'Editar Precio'
          //  'listar'        => 'Listar',
          //  'ver'           => 'Ver',
          //  'crear'         => 'Crear',
            //'guardar'       => 'Guardar', 
					//	'convertir_order_venta' => 'Convertir Orden de Venta',
						//'guardarOrdenVenta' => 'Guardar Orden De Venta'

	)
);
