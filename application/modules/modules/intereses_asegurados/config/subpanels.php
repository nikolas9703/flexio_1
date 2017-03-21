<?php defined('BASEPATH') || exit('No direct script access allowed');

//Lista de los subpaneles que se mostraran en la vista Detalle de Cliente.
$config['subpanels'] = [
	'solicitudes' => [
        'modulo' => 'solicitudes',
        'view' => 'solicitudes/tablatabsolicitudes',
        'nombre' => 'Solicitudes',
        'icono' => ''
    ],
	'polizas' => [
        'modulo' => 'polizas',
        'view' => 'intereses_asegurados/ocultotablapolizas',
        'nombre' => 'Polizas',
        'icono' => ''
    ],
	'documentos' => [
        'modulo' => 'documentos',
        'view' => 'documentos/ocultotablaseguros',
        'nombre' => 'Documentos',
        'icono' => ''
    ],
];
