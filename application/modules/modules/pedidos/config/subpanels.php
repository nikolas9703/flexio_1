<?php defined('BASEPATH') || exit('No direct script access allowed');

//Lista de los subpaneles que se mostraran en la vista Detalle de Caso.
//$config['subpanel'] = array(
//    'ordenes',
//    'facturas_compras',
//    'pagos',
//    'documentos'
//);

$config['subpanels'] = array(
    'pedidos.ordenes' => [
        'modulo' => 'ordenes',
        'view' => 'ocultotablaV2',
        'nombre' => 'O/C',
        'icono' => ''
    ],
    'pedidos.facturas' => [
        'modulo' => 'facturas_compras',
        'view' => 'ocultotablaV2',
        'nombre' => 'Factura',
        'icono' => ''
    ],
    'pedidos.pagos' => [
        'modulo' => 'pagos',
        'view' => 'ocultotablaV2',
        'nombre' => 'Pago',
        'icono' => ''
    ],
    'pedidos.documentos' => [
        'modulo'    => 'documentos',
        'view'      => 'ocultotablaV2',
        'nombre'    => 'Documentos',
        'icono'     => ''
    ]
);