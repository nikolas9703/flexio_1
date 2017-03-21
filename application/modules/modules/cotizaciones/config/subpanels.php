<?php defined('BASEPATH') || exit('No direct script access allowed');

//Lista de los subpaneles que se mostraran en la vista Detalle de Cliente.
$config['subpanel'] = array(
//	'ordenes_ventas',
//        'documentos'
);

$config['subpanels'] = array(
    'cotizacion.orden_venta' => [
        'modulo'    => 'ordenes_ventas',
        'view'      => 'ocultotablaV2',
        'nombre'    => 'Ordenes de venta',
        'icono'     => ''
    ],
    'cotizacion.documentos' => [
        'modulo'    => 'documentos',
        'view'      => 'ocultotabla',
        'nombre'    => 'Documentos',
        'icono'     => ''
    ]
);
