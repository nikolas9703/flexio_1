<?php defined('BASEPATH') || exit('No direct script access allowed');

//Lista de los subpaneles que se mostraran en la vista Detalle de Cliente.
$config['subpanels'] = [
	/*'polizas' => [
        'modulo' => 'polizas',
        'view' => 'polizas/ocultotablaintereses',
        'nombre' => 'Intereses Asegurados',
        'icono' => ''
    ],*/
	'documentos' => [
        'modulo' => 'documentos',
        'view' => 'documentos/ocultotablaseguros',
        'nombre' => 'Documentos',
        'icono' => ''
    ]
	/*'cobros_seguros' => [
        'modulo' => 'cobros_seguros',
        'view' => 'cobros_seguros/ocultotablatab',
        'nombre' => 'Cobros',
        'icono' => ''
    ],
    'renovaciones' => [
        'modulo' => 'renovaciones',
        'view' => 'polizas/ocultotablarenovaciones',
        'nombre' => 'Renovaciones',
        'icono' => ''
    ],
    'declaraciones' => [
        'modulo' => 'endoso',
        'view' => 'polizas/ocultoTabEndosos',
        'nombre' => 'Declaraciones',
        'icono' => ''
    ]*/
];

