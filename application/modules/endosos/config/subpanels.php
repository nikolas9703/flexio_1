<?php defined('BASEPATH') || exit('No direct script access allowed');

//Lista de los subpaneles que se mostraran en la vista Detalle de Cliente.
$config['subpanels'] = [
    'documentos' => [
        'modulo' => 'documentos',
        'view' => 'documentos/ocultotablaseguros',
        'nombre' => 'Documentos',
        'icono' => ''
    ],
	'polizas' => [
        'modulo' => 'polizas',
        //'view' => '',
        'view' => 'endosos/ocultotablaPrueba',
        //'view' => 'polizas/formulario',
        //11e7003e4d83e371b29dbc764e11d717
        'nombre' => 'Poliza',
        'icono' => ''
    ],
	
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
    ]*/
];


