<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['subpanels'] = [
	'centros_facturacion' => [
		'modulo' => 'clientes',
        'view' => 'ocultotabla_centros_facturacion',
        'nombre' => 'Centros de facturaci&oacute;n',
        'icono' => ''
	],
	'contactos' => [
		'modulo' => 'contactos',
        'view' => 'ocultotabla',
        'nombre' => 'Contactos',
        'icono' => ''
	],
	'oportunidades' => [
		'modulo' => 'oportunidades',
        'view' => 'ocultotabla',
        'nombre' => 'Oportunidades',
        'icono' => ''
	],
	'cotizaciones' => [
		'modulo' => 'cotizaciones',
        'view' => 'ocultotabla',
        'nombre' => 'Cotizaciones',
        'icono' => ''
	],
    'clientes_abonos' => [
        'modulo' => 'anticipos',
        'view' => 'ocultotabla',
        'nombre' => 'Anticipos',
        'icono' => ''
    ],
    'documentos' => [
        'modulo' => 'documentos',
        'view' => 'ocultotabla',
        'nombre' => 'Documentos',
        'icono' => ''
    ]
];
