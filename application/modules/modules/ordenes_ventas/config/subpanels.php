<?php defined('BASEPATH') || exit('No direct script access allowed');

//Lista de los subpaneles que se mostraran en la vista Detalle de Cliente.
//$config['subpanel'] = array(
//	'cotizaciones',
//        'documentos'
//);

$config['subpanels'] = array(
    'orden_venta.cotizacion' => [
        'modulo' => 'cotizaciones',
        'view' => 'ocultotablaV2',
        'nombre' => 'Cotizaciones',
        'icono' => ''
    ],
    'orden_venta.documentos' => [
        'modulo' => 'documentos',
        'view' => 'ocultotabla',
        'nombre' => 'Documentos',
        'icono' => ''
    ]
);
